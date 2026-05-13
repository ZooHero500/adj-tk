<template>
    <FeedLayout>
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
            :item-component="VideoPlayerTracking"
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
import { useRoute } from 'vue-router'
import { useProfileFeed } from '~/composables/useProfileFeed'
import { useFeedInteraction } from '~/composables/useFeedInteraction'
import { useHashids } from '@/composables/useHashids'
import FeedLayout from '~/layouts/FeedLayout.vue'
import SnapScrollFeed from '~/components/Feed/SnapScrollFeed.vue'
import VideoPlayerTracking from '~/components/Feed/VideoPlayerTracking.vue'
import axios from '~/plugins/axios'

const route = useRoute()
const { decodeHashid } = useHashids()
const { hasInteracted, handleFirstInteraction, globalMuted } = useFeedInteraction()

const snapFeedRef = ref(null)
const accountId = ref(null)
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
    } catch {
        profileError.value = true
    } finally {
        isLoadingProfile.value = false
    }
}

// Call useProfileFeed at the top level of setup — `enabled` guards against null accountId
const feedData = useProfileFeed(accountId)

const getVideoProps = (post, index) => ({
    duration: post.media?.duration,
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
