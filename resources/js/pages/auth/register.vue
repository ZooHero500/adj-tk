<template>
    <FullLayout>
        <div
            class="w-full flex items-center justify-center px-4"
            style="min-height: calc(100dvh - 60px)"
        >
            <div
                v-if="registrationClosed"
                class="max-w-md w-full bg-white/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl p-8 text-center backdrop-blur"
            >
                <div
                    class="mx-auto mb-4 flex size-30 items-center justify-center rounded-full bg-red-50 dark:bg-red-900/30"
                >
                    <LockClosedIcon class="size-20 text-red-500" />
                </div>

                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $t('auth.registrationsAreCurrentlyClosed') }}
                </h1>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('auth.thisServerIsntAccepting') }}
                </p>

                <div class="flex gap-3">
                    <AnimatedButton
                        class="flex-grow-1 mt-5"
                        variant="outline"
                        pill
                        @click="router.push('/')"
                    >
                        <div class="flex gap-2 items-center">
                            <ChevronLeftIcon class="w-5 h-5" />
                            {{ $t('common.goBackHome') }}
                        </div>
                    </AnimatedButton>

                    <AnimatedButton
                        v-if="showLoopsVideo"
                        class="flex-grow-1 mt-5"
                        variant="primaryGradient"
                        pill
                        @click="redirectToLoopsVideo"
                    >
                        <div class="flex gap-2 items-center">
                            <UserPlusIcon class="w-5 h-5 text-white" />
                            Join PornTk
                        </div>
                    </AnimatedButton>
                </div>
            </div>

            <div
                v-else-if="registrationCurated"
                class="max-w-md w-full bg-white/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl p-8 text-center backdrop-blur"
            >
                <div
                    class="mx-auto mb-4 flex size-30 items-center justify-center rounded-full bg-green-50 dark:bg-green-900/30"
                >
                    <ShieldCheckIcon class="size-20 text-green-500" />
                </div>

                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $t('auth.curatedOnboarding') }}
                </h1>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('auth.curatedOnboardingDesc') }}
                </p>

                <AnimatedButton
                    class="w-full mt-10"
                    variant="primaryGradient"
                    pill
                    @click="router.push('/auth/curated')"
                    size="lg"
                >
                    <div class="flex gap-2 items-center">
                        <UserPlusIcon class="w-5 h-5 text-white" />
                        {{ $t('auth.applyToJoin') }}
                    </div>
                </AnimatedButton>

                <AnimatedButton
                    class="w-full mt-5"
                    variant="outline"
                    size="lg"
                    pill
                    @click="router.push('/')"
                >
                    <div class="flex gap-2 items-center">
                        <ChevronLeftIcon class="w-5 h-5" />
                        {{ $t('common.goBackHome') }}
                    </div>
                </AnimatedButton>
            </div>

            <router-link
                v-else
                to="/"
                class="text-lg text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-700 px-6 py-2 rounded-xl"
            >
                {{ $t('common.goBackHome') }}
            </router-link>
        </div>
    </FullLayout>
</template>

<script setup>
import { onMounted, inject, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import FullLayout from '@/layouts/FullLayout.vue'
import AnimatedButton from '@/components/AnimatedButton.vue'
import {
    CheckCircleIcon,
    ChevronLeftIcon,
    LockClosedIcon,
    ShieldCheckIcon,
    UserPlusIcon
} from '@heroicons/vue/24/outline'

const authStore = inject('authStore')
const appConfig = inject('appConfig')

const route = useRoute()
const router = useRouter()

const registrationClosed = computed(() => appConfig && appConfig.registration_mode === 'closed')
const registrationCurated = computed(() => appConfig && appConfig.registration_mode === 'curated')
const showLoopsVideo = computed(() => appConfig && appConfig.app.url != 'https://porntk.com')

const redirectToLoopsVideo = () => {
    window.location.href = 'https://porntk.com/register'
}

onMounted(() => {
    if (authStore.authenticated) {
        router.push('/')
    } else if (registrationClosed.value || registrationCurated.value) {
    } else {
        router.replace('/')
        authStore.openAuthModal('register')
    }
})
</script>
