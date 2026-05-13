<template>
    <div class="min-h-screen bg-white dark:bg-neutral-950">
        <!-- Splash Screen -->
        <Transition name="splash-fade">
            <div v-if="showSplash" class="splash-screen">
                <div class="splash-logo">
                    <img src="/img/logo-light.svg" alt="PornTk" class="splash-logo-img" />
                </div>
            </div>
        </Transition>

        <router-view v-slot="{ Component }">
            <Suspense>
                <template #default>
                    <component :is="Component" />
                </template>
                <template #fallback>
                    <PageSkeleton />
                </template>
            </Suspense>
        </router-view>
        <AuthModal v-if="authStore.isOpen" :mode="authStore.authMode" />
    </div>
</template>

<script setup>
import { ref, onMounted, watch, inject } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AuthModal from '@/components/AuthModal.vue'
import PageSkeleton from '@/components/Layout/PageSkeleton.vue'

const authStore = useAuthStore()
const showSplash = ref(true)

onMounted(async () => {
    if (localStorage.theme === 'light') {
        document.documentElement.classList.remove('dark')
    } else {
        document.documentElement.classList.add('dark')
    }

    setTimeout(() => {
        showSplash.value = false
    }, 1500)

    try {
        await authStore.hasSessionExpired()
    } catch (error) {
        console.error('Error in App setup:', error)
    }
})
</script>

<style>
.splash-screen {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0a0a0a;
}

.splash-logo {
    animation: splash-bounce 1.5s ease-in-out;
}

.splash-logo-img {
    width: 80px;
    height: 80px;
    border-radius: 20px;
}

@keyframes splash-bounce {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    20% {
        transform: scale(1.15);
        opacity: 1;
    }
    35% {
        transform: scale(0.9);
    }
    50% {
        transform: scale(1.05);
    }
    65% {
        transform: scale(0.97);
    }
    80% {
        transform: scale(1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.splash-fade-leave-active {
    transition: opacity 0.3s ease-out;
}
.splash-fade-leave-to {
    opacity: 0;
}
</style>
