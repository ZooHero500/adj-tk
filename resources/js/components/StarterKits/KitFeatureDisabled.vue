<template>
    <div
        class="min-h-screen bg-[#FAFAFA] dark:bg-neutral-950 font-body lg:-mt-5 lg:-mr-2.5 w-full h-full flex flex-col justify-center items-center"
    >
        <div class="flex flex-col justify-center items-center">
            <div class="relative mb-6">
                <div
                    class="w-20 h-20 rounded-3xl flex items-center justify-center bg-primary/8 dark:bg-primary/12"
                >
                    <ExclamationTriangleIcon class="w-9 h-9 text-primary/40" />
                </div>
            </div>
            <h2
                class="font-display text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2"
            >
                {{ $t('common.starterKitsUnavailable') }}
            </h2>
            <p
                class="font-body text-gray-500 dark:text-gray-400 text-base max-w-xl leading-relaxed mb-1"
            >
                {{ $t('common.theStarterKitsFeatureHasBeenDisabledByTheAdminTeam') }}.
            </p>
            <p
                class="font-body text-sm text-gray-400 dark:text-gray-500 max-w-xl leading-relaxed mb-8"
            >
                {{ $t('common.pleaseContactThemForAssistance') }}.
            </p>
            <AnimatedButton @click="router.push('/')">
                <div class="flex items-center gap-3">
                    <HomeIcon class="w-5 h-5" />
                    {{ $t('common.goBackHome') }}
                </div>
            </AnimatedButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ExclamationTriangleIcon, HomeIcon } from '@heroicons/vue/24/outline'
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}
</style>
