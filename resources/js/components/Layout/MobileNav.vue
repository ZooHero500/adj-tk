<template>
    <nav
        v-if="isMobileView"
        class="fixed bottom-0 left-0 right-0 z-50 h-[56px] border-t bg-black/90 backdrop-blur-md border-white/10 safe-area-bottom"
    >
        <div class="flex items-center justify-around h-full px-4">
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
                    isActive('/explore') || isActive('/search')
                        ? 'text-white'
                        : 'text-gray-400'
                "
            >
                <i class="text-[26px]" :class="isActive('/explore') || isActive('/search') ? 'bx bxs-search' : 'bx bx-search'"></i>
            </router-link>

            <router-link
                to="/studio/upload"
                class="flex items-center justify-center w-12 h-12 transition-colors text-gray-400"
            >
                <i class="bx bx-download text-[26px]"></i>
            </router-link>

            <router-link
                v-if="authStore.isAuthenticated"
                :to="`/@${authStore.user.username}?tab=bookmarks`"
                class="flex items-center justify-center w-12 h-12 transition-colors"
                :class="
                    isActive('/bookmarks')
                        ? 'text-white'
                        : 'text-gray-400'
                "
            >
                <i class="bx bx-bookmark text-[26px]"></i>
            </router-link>
            <router-link
                v-else
                to="/login"
                class="flex items-center justify-center w-12 h-12 transition-colors text-gray-400"
            >
                <i class="bx bx-bookmark text-[26px]"></i>
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
                <i class="text-[26px]" :class="isActive(`/@${authStore.user.username}`) ? 'bx bxs-user' : 'bx bx-user'"></i>
            </router-link>
            <router-link
                v-else
                to="/login"
                class="flex items-center justify-center w-12 h-12 transition-colors text-gray-400"
            >
                <i class="bx bx-user text-[26px]"></i>
            </router-link>
        </div>
    </nav>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'

const route = useRoute()
const authStore = useAuthStore()

const isMobileView = ref(false)

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
