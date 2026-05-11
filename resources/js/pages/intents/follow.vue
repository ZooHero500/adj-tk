<template>
    <BlankLayout>
        <div class="min-h-screen bg-gray-50 dark:bg-slate-900 py-8 px-4">
            <div class="max-w-md mx-auto">
                <div v-if="isLoading" class="text-center py-12">
                    <Spinner />
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-4">
                        {{ $t('common.loading') }}
                    </p>
                </div>

                <div
                    v-else-if="!authStore.isAuthenticated"
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden"
                >
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-center">
                        <UserIcon class="w-12 h-12 text-white mx-auto mb-2" />
                        <h1 class="text-xl font-bold text-white">Login Required</h1>
                    </div>

                    <div class="p-6">
                        <div class="text-center mb-6">
                            <h2
                                class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-2"
                            >
                                Sign in to Follow
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-slate-400">
                                You need to be logged in to follow users on PornTk. Create an account
                                or sign in to continue.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <AnimatedButton
                                class="w-full"
                                size="lg"
                                @click="authStore.openAuthModal('login', fullPath)"
                            >
                                <div class="flex items-center justify-center gap-2">
                                    <UserIcon class="w-5 h-5" />
                                    Sign In
                                </div>
                            </AnimatedButton>

                            <button
                                @click="handleCancel"
                                class="w-full py-3 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 font-medium transition-colors cursor-pointer"
                            >
                                {{ $t('common.cancel') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    v-else-if="error"
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden"
                >
                    <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 text-center">
                        <ExclamationCircleIcon class="w-12 h-12 text-white mx-auto mb-2" />
                        <h1 class="text-xl font-bold text-white">Unable to Load Profile</h1>
                    </div>

                    <div class="p-6">
                        <div class="text-center mb-6">
                            <h2
                                class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-2"
                            >
                                User Not Found
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-slate-400">
                                This user doesn't exist or cannot be accessed at this time. The
                                account may have been deleted, made private, or the link may be
                                incorrect.
                            </p>
                        </div>

                        <div
                            v-if="error"
                            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6"
                        >
                            <p class="text-sm text-red-600 dark:text-red-400 text-center">
                                {{ error }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <AnimatedButton class="w-full" size="lg" @click="router.push('/')">
                                <div class="flex items-center justify-center gap-2">
                                    <HomeIcon class="w-5 h-5" />
                                    Go Home
                                </div>
                            </AnimatedButton>

                            <button
                                @click="fetchActor"
                                class="w-full py-3 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 font-medium transition-colors cursor-pointer"
                            >
                                Try Again
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    v-else-if="actor"
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden"
                >
                    <div class="bg-gradient-to-br from-[#F02C56] to-[#F02C56]/80 p-6 text-center">
                        <UserPlusIcon class="w-12 h-12 text-white mx-auto mb-2" />
                        <h1 class="text-xl font-bold text-white">
                            {{ getHeaderText }}
                        </h1>
                    </div>

                    <div class="p-6">
                        <div class="flex items-start space-x-4 mb-6">
                            <img
                                :src="actor.avatar || '/storage/avatars/default.jpg'"
                                :alt="actor.username"
                                class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 dark:border-slate-700"
                            />
                            <div class="flex-1 min-w-0">
                                <h2
                                    class="text-lg font-bold text-gray-900 dark:text-slate-100 truncate"
                                >
                                    {{ actor.name || actor.username }}
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-slate-400 truncate">
                                    @{{ actor.username }}
                                </p>
                                <p
                                    v-if="profileStore.followerCount !== undefined"
                                    class="text-xs text-gray-500 dark:text-slate-400 mt-1"
                                >
                                    {{ formatCount(profileStore.followerCount) }}
                                    {{ $t('common.followers') }}
                                </p>
                            </div>
                        </div>

                        <p
                            v-if="actor.bio"
                            class="text-sm text-gray-700 dark:text-slate-300 mb-6 line-clamp-3"
                        >
                            {{ actor.bio }}
                        </p>

                        <div class="flex flex-col gap-4">
                            <div
                                v-if="profileStore.isSelf"
                                class="w-full py-3 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-medium rounded-lg text-center flex items-center justify-center gap-2"
                            >
                                <UserIcon class="w-5 h-5" />
                                This is your profile
                            </div>

                            <template v-else>
                                <AnimatedButton
                                    v-if="profileStore.isFollowing"
                                    class="w-full"
                                    size="lg"
                                    :disabled="isSubmitting"
                                    @click="handleUnfollow"
                                >
                                    <div class="flex">
                                        <CheckIcon class="w-5 h-5 mr-2" />
                                        {{
                                            isSubmitting
                                                ? $t('common.processing')
                                                : $t('common.following')
                                        }}
                                    </div>
                                </AnimatedButton>

                                <AnimatedButton
                                    v-else-if="profileStore.isFollowingRequestPending"
                                    class="w-full"
                                    size="lg"
                                    :disabled="isSubmitting"
                                    @click="handleUndoRequest"
                                >
                                    <div class="flex">
                                        <ClockIcon class="w-5 h-5 mr-2" />
                                        {{ isSubmitting ? $t('common.processing') : 'Requested' }}
                                    </div>
                                </AnimatedButton>

                                <AnimatedButton
                                    v-else
                                    class="w-full"
                                    size="lg"
                                    :disabled="isSubmitting"
                                    @click="handleFollow"
                                >
                                    <div class="flex">
                                        <UserPlusIcon class="w-5 h-5 mr-2" />
                                        {{
                                            isSubmitting
                                                ? $t('common.following')
                                                : $t('common.follow')
                                        }}
                                    </div>
                                </AnimatedButton>
                            </template>

                            <a
                                :href="actor.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block w-full py-3 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-900 dark:text-slate-100 font-medium rounded-lg transition-colors text-center cursor-pointer flex items-center justify-center gap-2"
                            >
                                <ArrowTopRightOnSquareIcon class="w-5 h-5" />
                                View Profile
                            </a>

                            <button
                                @click="handleCancel"
                                :disabled="isSubmitting"
                                class="w-full py-3 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 font-medium transition-colors cursor-pointer"
                            >
                                {{ $t('common.cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BlankLayout>
</template>

<script setup>
import { ref, onMounted, inject, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useHashids } from '@/composables/useHashids'
import { useUtils } from '@/composables/useUtils'
import axios from '@/plugins/axios'
import AnimatedButton from '@/components/AnimatedButton.vue'
import {
    UserPlusIcon,
    CheckIcon,
    ClockIcon,
    ArrowTopRightOnSquareIcon,
    UserIcon,
    HomeIcon,
    ExclamationCircleIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const appStore = inject('appStore')
const profileStore = inject('profileStore')
const { decodeHashid } = useHashids()
const axiosInstance = axios.getAxiosInstance()
const { formatCount } = useUtils()

const actor = ref(null)
const isLoading = ref(true)
const isSubmitting = ref(false)
const error = ref(null)
const fullPath = computed(() => route.fullPath)

const getHeaderText = computed(() => {
    if (profileStore.isSelf) {
        return 'Your Profile'
    }
    if (profileStore.isFollowing) {
        return 'Following User'
    }
    if (profileStore.isFollowingRequestPending) {
        return 'Follow Request Pending'
    }
    return 'Follow User'
})

const fetchActor = async () => {
    try {
        isLoading.value = true
        error.value = null

        const objectUrl = route.query.object
        if (!objectUrl) {
            throw new Error('Missing object parameter')
        }

        const response = await axiosInstance.post('/api/v1/intents/follow/account', {
            query: encodeURI(objectUrl)
        })

        if (![200, 201].includes(response.status)) {
            throw new Error('Failed to fetch actor')
        }

        const user = response.data.data
        if (!user) {
            throw new Error('Account not found')
        }

        actor.value = user

        try {
            await profileStore.getProfile(user.username)
            await profileStore.getProfileState(user.id)
        } catch (profileError) {
            console.warn('Could not fetch complete profile state:', profileError)
        }
    } catch (err) {
        console.error('Error fetching actor:', err)
        error.value =
            err.response?.data?.message || err.message || 'Unable to load user information'
    } finally {
        isLoading.value = false
    }
}

const handleFollow = async () => {
    if (!authStore.isAuthenticated) {
        authStore.openAuthModal('login', fullPath.value)
        return
    }

    if (profileStore.isSelf) {
        return
    }

    try {
        isSubmitting.value = true
        await profileStore.follow()

        handleSuccessRedirect()
    } catch (err) {
        console.error('Error following user:', err)
        error.value = 'Failed to follow user. Please try again.'
    } finally {
        isSubmitting.value = false
    }
}

const handleUnfollow = async () => {
    if (!authStore.isAuthenticated) {
        authStore.openAuthModal('login', fullPath.value)
        return
    }

    try {
        isSubmitting.value = true
        await profileStore.unfollow()
    } catch (err) {
        console.error('Error unfollowing user:', err)
        error.value = 'Failed to unfollow user. Please try again.'
    } finally {
        isSubmitting.value = false
    }
}

const handleUndoRequest = async () => {
    if (!authStore.isAuthenticated) {
        authStore.openAuthModal('login', fullPath.value)
        return
    }

    try {
        isSubmitting.value = true
        await profileStore.undoFollowRequest()
    } catch (err) {
        console.error('Error canceling follow request:', err)
        error.value = 'Failed to cancel follow request. Please try again.'
    } finally {
        isSubmitting.value = false
    }
}

const handleSuccessRedirect = () => {
    const onSuccess = route.query['on-success']
    if (onSuccess === '(close)') {
        window.close()
    } else if (onSuccess) {
        window.location.href = onSuccess
    } else {
        router.push(`/@${actor.value.username}`)
    }
}

const handleCancel = () => {
    const onCancel = route.query['on-cancel']
    if (onCancel === '(close)') {
        window.close()
    } else if (onCancel) {
        window.location.href = onCancel
    } else {
        router.push('/')
    }
}

onMounted(() => {
    if (!authStore.isAuthenticated) {
        isLoading.value = false
        return
    }
    fetchActor()
})
</script>
