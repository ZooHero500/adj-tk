<template>
    <FeedLayout>
        <!-- Floating header: back button + username -->
        <div
            v-if="!isLoadingProfile && !profileError && accountId"
            class="fixed top-0 left-0 right-0 z-50 pointer-events-none lg:left-[260px]"
        >
            <div class="flex items-center justify-between h-14 px-4 pointer-events-auto safe-area-top">
                <button
                    @click="goBack"
                    class="flex items-center justify-center w-9 h-9 rounded-full bg-black/50 backdrop-blur-sm hover:bg-black/70 transition-colors"
                >
                    <ChevronLeftIcon class="h-5 w-5 text-white" />
                </button>

                <router-link
                    :to="`/@${profileUsername}`"
                    class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-black/50 backdrop-blur-sm hover:bg-black/70 transition-colors"
                >
                    <img
                        v-if="profileAvatar"
                        :src="profileAvatar"
                        :alt="profileUsername"
                        class="w-6 h-6 rounded-full"
                        @error="$event.target.src = '/storage/avatars/default.jpg'"
                    />
                    <span class="text-white text-sm font-semibold">@{{ profileUsername }}</span>
                </router-link>

                <div class="w-9"></div>
            </div>
        </div>

        <div
            v-if="isLoadingProfile"
            class="flex h-screen flex-col items-center justify-center dark:bg-black"
        >
            <Spinner class="h-12 w-12" />
            <p class="text-gray-500 dark:text-gray-400 mt-4 text-sm">
                {{ $t('post.loadingVideoDotDotDot') }}
            </p>
        </div>

        <div
            v-else-if="profileError"
            class="flex h-screen flex-col items-center justify-center dark:bg-black px-6"
        >
            <div class="text-6xl mb-4">😵</div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">
                {{ $t('profile.profileNotFound') }}
            </h2>
            <button
                @click="$router.push('/')"
                class="mt-4 bg-primary hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg"
            >
                {{ $t('post.goHome') }}
            </button>
        </div>

        <SnapScrollFeed
            v-else-if="accountId"
            ref="snapFeedRef"
            :key="`profile-feed-${accountId}`"
            :feed-data="feedData"
            :item-component="VideoPlayer"
            :get-item-props="getVideoProps"
            :get-item-key="getVideoKey"
            :auto-play="hasInteracted"
            :scroll-threshold="1.5"
            :snap-sensitivity="50"
            @item-visible="onVideoVisible"
            @item-hidden="onVideoHidden"
            @interaction="onUserInteraction"
        />
        <HideCommentConfirmModal />
    </FeedLayout>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProfileFeed } from '~/composables/useProfileFeed'
import { useFeedInteraction } from '~/composables/useFeedInteraction'
import { useHashids } from '@/composables/useHashids'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'
import FeedLayout from '~/layouts/FeedLayout.vue'
import SnapScrollFeed from '~/components/Feed/SnapScrollFeed.vue'
import VideoPlayer from '~/components/Feed/VideoPlayer.vue'
import axios from '~/plugins/axios'

const route = useRoute()
const router = useRouter()
const { decodeHashid } = useHashids()
const { hasInteracted, handleFirstInteraction, globalMuted } = useFeedInteraction()

const snapFeedRef = ref(null)
const accountId = ref(null)
const profileUsername = ref(route.params.username)
const profileAvatar = ref(null)
const isLoadingProfile = ref(true)
const profileError = ref(false)
const hasScrolledToTarget = ref(false)

const targetVideoId = computed(() => {
    try {
        return decodeHashid(route.params.videoId)
    } catch {
        return null
    }
})

const goBack = () => {
    if (window.history.length > 1) {
        router.back()
    } else {
        router.push(`/@${profileUsername.value}`)
    }
}

const loadProfile = async () => {
    const username = route.params.username
    if (!username) {
        profileError.value = true
        isLoadingProfile.value = false
        return
    }

    try {
        const axiosInstance = axios.getAxiosInstance()
        const res = await axiosInstance.get(`/api/v1/account/username/${username}?ext=1`)
        accountId.value = res.data.data.id
        profileUsername.value = res.data.data.username
        profileAvatar.value = res.data.data.avatar
    } catch {
        profileError.value = true
    } finally {
        isLoadingProfile.value = false
    }
}

// Call useProfileFeed at the top level of setup — `enabled` guards against null accountId
const feedData = useProfileFeed(accountId)

const getVideoProps = (post, index) => ({
    'video-id': post.id,
    'video-url': post.media.src_url,
    'hls-url': post.media.hls_url || null,
    'share-url': post.url,
    'profile-id': post.account.id,
    username: post.account.username,
    'profile-image': post.account.avatar,
    caption: post.caption,
    hashtags: post.tags,
    mentions: post.mentions,
    likes: post.likes,
    hasLiked: post.has_liked,
    hasBookmarked: post.has_bookmarked,
    bookmarks: post.bookmarks,
    shares: post.shares,
    comments: [],
    canComment: post.permissions?.can_comment,
    'comment-count': post.comments,
    index: index,
    isSensitive: post?.is_sensitive,
    altText: post?.media.alt_text,
    autoPlay: hasInteracted.value,
    muted: globalMuted.value
})

const getVideoKey = (post) => post.id

const onVideoVisible = () => {}
const onVideoHidden = () => {}
const onUserInteraction = () => {
    handleFirstInteraction()
}

// After feed data loads, scroll to the target video
watch(
    () => feedData.data?.value?.pages,
    async (pages) => {
        if (!pages || hasScrolledToTarget.value || !targetVideoId.value) return

        let globalIndex = 0
        for (const page of pages) {
            for (const post of page.data || []) {
                if (String(post.id) === String(targetVideoId.value)) {
                    hasScrolledToTarget.value = true
                    await nextTick()
                    setTimeout(() => {
                        snapFeedRef.value?.scrollToItem(globalIndex)
                    }, 300)
                    return
                }
                globalIndex++
            }
        }
    },
    { deep: true, immediate: true }
)

loadProfile()
</script>

<style scoped>
.safe-area-top {
    padding-top: env(safe-area-inset-top);
}
</style>
