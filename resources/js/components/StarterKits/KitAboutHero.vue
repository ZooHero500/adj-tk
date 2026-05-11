<template>
    <section
        class="relative min-h-[80dvh] md:min-h-[85dvh] lg:min-h-[90dvh] xl:min-h-[95dvh] flex flex-col items-center justify-center text-center px-6 pt-16 pb-24 overflow-hidden"
    >
        <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
            <div class="glow-blob glow-a"></div>
            <div class="glow-blob glow-b"></div>
            <div
                class="absolute inset-0 dark:opacity-[0.025] opacity-[0.04]"
                style="
                    background-image:
                        linear-gradient(#888 1px, transparent 1px),
                        linear-gradient(90deg, #888 1px, transparent 1px);
                    background-size: 30px 90px;
                "
            ></div>
        </div>

        <!-- <div class="relative z-10 mb-8 fade-up" style="animation-delay: 0ms">
            <span
                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium border dark:border-white/12 border-black/10 dark:bg-white/4 bg-black/4">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FF6347] animate-pulse"></span>
                <span class="dark:text-white/70 text-black/60">New on PornTk</span>
            </span>
        </div> -->

        <div class="relative z-10 mb-7 fade-up" style="animation-delay: 80ms">
            <h1 class="font-display font-black leading-[0.88] tracking-tighter">
                <span class="block" style="font-size: clamp(3.5rem, 8vw, 8.5rem)"
                    >{{ t('common.fillYourFeed') }}.</span
                >
                <span class="block gradient-text" style="font-size: clamp(3.5rem, 8vw, 8.5rem)"
                    >{{ t('common.instantly') }}.</span
                >
            </h1>
        </div>

        <p
            class="relative z-10 text-lg md:text-xl dark:text-white/55 text-black/55 max-w-2xl mx-auto mb-2 leading-relaxed fade-up"
            style="animation-delay: 160ms"
        >
            {{
                t('common.starterKitsAreCuratedCollectionsOfLoopsCreatorsOrganizedByTheCommunity')
            }}.
        </p>

        <p
            class="relative z-10 text-lg md:text-xl dark:text-white/55 text-black/55 max-w-2xl mx-auto mb-10 leading-relaxed fade-up"
            style="animation-delay: 160ms"
        >
            {{ t('common.followAWholeWorldOfContentInOneTap') }}.
        </p>

        <div
            class="relative z-10 flex items-center gap-3 flex-wrap justify-center fade-up"
            style="animation-delay: 240ms"
        >
            <template v-if="isAuth">
                <AnimatedButton
                    size="lg"
                    class="rounded-full"
                    :pill="true"
                    variant="primaryGradient"
                    @click="router.push('/starter-kits')"
                >
                    <div class="flex items-center gap-2">
                        Starter Kits Home
                        <ArrowRightIcon class="w-4 h-4" />
                    </div>
                </AnimatedButton>

                <AnimatedButton
                    variant="primaryOutline"
                    size="lg"
                    :pill="true"
                    @click="router.push('/starter-kits/create')"
                >
                    <div class="flex items-center gap-2">
                        <PlusIcon class="w-4 h-4" />
                        {{ t('common.createAStarterKit') }}
                    </div>
                </AnimatedButton>
            </template>
            <template v-else>
                <AnimatedButton
                    size="lg"
                    variant="primaryGradient"
                    :pill="true"
                    @click="router.push('/starter-kits/browse')"
                >
                    <div class="flex items-center gap-2">
                        {{ t('common.browseKits') }}
                        <ArrowRightIcon class="w-4 h-4" />
                    </div>
                </AnimatedButton>
            </template>
        </div>

        <div
            class="absolute bottom-8 left-1/2 -translate-x-1/2 opacity-30 animate-bounce"
            aria-hidden="true"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="22"
                height="22"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
            >
                <polyline points="6 9 12 15 18 9" />
            </svg>
        </div>
    </section>
</template>

<script setup>
import { ArrowRightIcon, PlusIcon } from '@heroicons/vue/24/outline'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import AnimatedButton from '../AnimatedButton.vue'

const props = defineProps({
    isAuth: {
        type: Boolean,
        required: true
    }
})

const { t } = useI18n()
const router = useRouter()
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}

.glow-blob {
    position: absolute;
    border-radius: 9999px;
    pointer-events: none;
    filter: blur(80px);
}

.glow-a {
    width: 700px;
    height: 700px;
    left: -180px;
    top: -180px;
    background: radial-gradient(circle, rgba(255, 99, 71, 0.22) 0%, transparent 70%);
}

.glow-b {
    width: 550px;
    height: 550px;
    right: -100px;
    bottom: -80px;
    background: radial-gradient(circle, rgba(255, 20, 120, 0.18) 0%, transparent 70%);
}

:not(.dark) .glow-a {
    opacity: 0.45;
}

:not(.dark) .glow-b {
    opacity: 0.35;
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(24px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-up {
    opacity: 0;
    animation: fadeUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.gradient-text {
    background: linear-gradient(130deg, #ff6347 0%, #ff1478 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
