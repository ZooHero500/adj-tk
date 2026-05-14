<template>
    <nav
        v-if="isMobileView"
        class="fixed bottom-0 left-0 right-0 z-50 h-[56px] border-t bg-black/90 backdrop-blur-md border-white/10 safe-area-bottom"
    >
        <div class="flex items-center justify-around h-full px-2">
            <router-link
                to="/"
                class="flex items-center justify-center w-12 h-12 transition-colors"
                :class="
                    isActive('/')
                        ? 'text-white'
                        : 'text-gray-400'
                "
            >
                <i class="text-[26px]" :class="isActive('/') ? 'bx bxs-home' : 'bx bx-home'"></i>
            </router-link>

            <router-link
                to="/explore"
                class="flex items-center justify-center w-12 h-12 transition-colors"
                :class="
                    isActive('/explore')
                        ? 'text-white'
                        : 'text-gray-400'
                "
            >
                <i
                    class="text-[26px]"
                    :class="isActive('/explore') ? 'bx bxs-compass' : 'bx bx-compass'"
                ></i>
            </router-link>

            <router-link
                to="/studio/upload"
                class="relative flex items-center justify-center px-5"
            >
                <div
                    class="relative w-11 h-8 rounded-lg overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 shadow-md"
                >
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg
                            class="w-6 h-6 text-gray-700 dark:text-gray-200"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                    </div>
                </div>
            </router-link>

            <router-link
                to="/notifications"
                class="relative flex items-center justify-center w-12 h-12 transition-colors"
                :class="
                    isActive('/notifications')
                        ? 'text-white'
                        : 'text-gray-400'
                "
            >
                <div class="relative">
                    <i
                        class="text-[26px]"
                        :class="isActive('/notifications') ? 'bx bxs-bell' : 'bx bx-bell'"
                    ></i>
                    <span
                        v-if="unreadCount > 0"
                        class="absolute -top-1 -right-1 flex items-center justify-center min-w-[16px] h-[16px] px-0.5 text-[9px] font-bold text-white bg-primary rounded-full"
                    >
                        {{ unreadCount > 99 ? '99+' : unreadCount }}
                    </span>
                </div>
            </router-link>

            <router-link
                v-if="authStore.isAuthenticated"
                :to="`/@${authStore.user.username}`"
                class="flex items-center justify-center w-12 h-12 transition-colors"
                :class="
                    isActive(`/@${authStore.user.username}`)
                        ? 'text-white'
                        : 'text-gray-400'
                "
            >
                <img
                    class="rounded-full w-7 h-7"
                    :src="authStore.user.avatar"
                    alt="User avatar"
                    @error="$event.target.src = '/storage/avatars/default.jpg'"
                />
            </router-link>
            <router-link
                v-else
                to="/me"
                class="flex items-center justify-center w-12 h-12 transition-colors"
                :class="isActive('/me') ? 'text-white' : 'text-gray-400'"
            >
                <i class="bx bx-user text-[26px]"></i>
            </router-link>
        </div>
    </nav>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@/stores/notifications.js'
import { useAuthStore } from '@/stores/auth.js'

const route = useRoute()
const notificationStore = useNotificationStore()
const authStore = useAuthStore()

const isMobileView = ref(false)
const unreadCount = computed(() => notificationStore.unreadCount)

const isActive = (path) => {
    if (path === '/') {
        return route.path === '/' || route.path === '/feed/for-you'
    }
    return route.path.startsWith(path)
}

const checkMobileView = () => {
    isMobileView.value = window.innerWidth < 768
}

onMounted(() => {
    checkMobileView()
    window.addEventListener('resize', checkMobileView)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobileView)
})
</script>

<style scoped>
.safe-area-bottom {
    padding-bottom: env(safe-area-inset-bottom);
}
</style>
