<template>
    <MainLayout>
        <div class="pt-[30px] px-5">
            <!-- Avatar + info -->
            <div class="flex flex-col items-center">
                <div
                    class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-gray-200 dark:bg-neutral-800 flex items-center justify-center mb-4 border border-gray-200 dark:border-neutral-700"
                >
                    <i class="bx bx-user text-gray-400 dark:text-neutral-600" style="font-size: 48px"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $t('guestProfile.title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('guestProfile.subtitle') }}
                </p>
            </div>

            <!-- Auth buttons -->
            <div class="mt-6 space-y-3 max-w-xs mx-auto">
                <AnimatedButton
                    v-if="appConfig.registration"
                    @click="authStore.openAuthModal('register')"
                    class="w-full rounded-xl"
                    variant="primary"
                >
                    <div class="text-base">{{ $t('common.createAccount') }}</div>
                </AnimatedButton>
                <AnimatedButton
                    @click="authStore.openAuthModal('login')"
                    class="w-full rounded-xl"
                    variant="light"
                >
                    <div class="text-base">{{ $t('nav.logIn') }}</div>
                </AnimatedButton>
            </div>

            <!-- Tab bar (empty) -->
            <div class="mt-8 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex">
                    <button
                        class="px-6 py-3 text-xs font-semibold text-black dark:text-white relative"
                    >
                        {{ $t('common.videos') }}
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-black dark:bg-white"></div>
                    </button>
                </div>
            </div>

            <!-- Empty state -->
            <div class="flex flex-col items-center justify-center py-16">
                <div class="text-6xl mb-4">📹</div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">
                    {{ $t('guestProfile.noVideosTitle') }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 text-center text-sm">
                    {{ $t('guestProfile.noVideosSubtitle') }}
                </p>
            </div>

            <div class="w-full h-20"></div>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '~/layouts/MainLayout.vue'
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const appConfig = inject('appConfig')

onMounted(() => {
    if (authStore.authenticated) {
        router.replace(`/@${authStore.user.username}`)
    }
})
</script>
