<template>
    <div class="min-h-screen bg-white dark:bg-neutral-950 flex" role="status" aria-label="Loading">
        <aside
            class="hidden lg:flex flex-col w-60 xl:w-72 shrink-0 border-r border-neutral-200 dark:border-neutral-800 h-screen sticky top-0 py-4"
        >
            <div class="px-6 pb-6">
                <div class="skeleton h-8 w-24 rounded-md" />
            </div>

            <nav class="px-3 space-y-1">
                <div
                    v-for="n in 6"
                    :key="`nav-${n}`"
                    class="flex items-center gap-4 px-3 py-3 rounded-lg"
                >
                    <div class="skeleton h-6 w-6 rounded-md shrink-0" />
                    <div
                        class="skeleton h-4 rounded-md"
                        :style="{ width: `${[70, 55, 65, 50, 60, 75][n - 1]}px` }"
                    />
                </div>
            </nav>

            <div class="mx-6 my-4 border-t border-neutral-200 dark:border-neutral-800" />

            <div class="px-6 pt-4 space-y-2">
                <div class="skeleton h-2.5 w-full rounded-md" />
                <div class="skeleton h-2.5 w-3/4 rounded-md" />
                <div class="skeleton h-2.5 w-5/6 rounded-md" />
            </div>

            <div class="px-6 pt-6 space-y-2">
                <div class="skeleton h-1.5 w-32 rounded-md" />
                <div class="skeleton h-1.5 w-32 rounded-md" />
            </div>
        </aside>

        <main
            class="flex-1 min-w-0 flex flex-col items-center justify-start lg:justify-center py-6 px-4"
        >
            <div class="lg:hidden w-full flex items-center justify-between mb-4">
                <div class="skeleton h-7 w-20 rounded-md" />
                <div class="flex gap-3">
                    <div class="skeleton h-8 w-8 rounded-full" />
                    <div class="skeleton h-8 w-8 rounded-full" />
                </div>
            </div>

            <div class="flex items-end gap-3 sm:gap-4">
                <div class="relative">
                    <div class="skeleton w-[min(90vw,480px)] aspect-[9/16] rounded-xl" />
                    <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 space-y-2">
                        <div class="skeleton-overlay h-4 w-32 rounded-md" />
                        <div class="skeleton-overlay h-3 w-48 rounded-md" />
                        <div class="skeleton-overlay h-3 w-40 rounded-md" />
                        <div class="pt-1">
                            <div class="skeleton-overlay h-3 w-28 rounded-md" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-5 sm:gap-6 pb-2">
                    <div class="relative">
                        <div class="skeleton h-14 w-14 rounded-full" />
                    </div>

                    <div
                        v-for="n in 4"
                        :key="`action-${n}`"
                        class="flex flex-col items-center gap-1.5"
                    >
                        <div class="skeleton h-10 w-10 rounded-full" />
                        <div class="skeleton h-2.5 w-8 rounded-md" />
                    </div>

                    <div class="skeleton h-10 w-10 rounded-full" />
                </div>
            </div>
        </main>

        <span class="sr-only">Loading feed, please wait</span>
    </div>
</template>

<script setup></script>

<style scoped>
.skeleton,
.skeleton-overlay {
    position: relative;
    overflow: hidden;
}

.skeleton {
    background-color: rgb(229 229 229);
    /* neutral-200 */
}

:global(.dark) .skeleton {
    background-color: rgb(38 38 38);
    /* neutral-800 */
}

/* Overlay variant sits on top of the dark video placeholder, so it needs
   its own tint that reads against both light and dark backgrounds. */
.skeleton-overlay {
    background-color: rgba(255, 255, 255, 0.2);
}

:global(.dark) .skeleton-overlay {
    background-color: rgba(255, 255, 255, 0.12);
}

.skeleton::after,
.skeleton-overlay::after {
    content: '';
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background-image: linear-gradient(
        90deg,
        transparent 0,
        rgba(255, 255, 255, 0.5) 50%,
        transparent 100%
    );
    animation: shimmer 1.6s infinite;
}

:global(.dark) .skeleton::after {
    background-image: linear-gradient(
        90deg,
        transparent 0,
        rgba(255, 255, 255, 0.06) 50%,
        transparent 100%
    );
}

.skeleton-overlay::after {
    background-image: linear-gradient(
        90deg,
        transparent 0,
        rgba(255, 255, 255, 0.25) 50%,
        transparent 100%
    );
}

@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .skeleton::after,
    .skeleton-overlay::after {
        animation: none;
    }
}
</style>
