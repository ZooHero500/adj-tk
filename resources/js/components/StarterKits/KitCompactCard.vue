<template>
    <div
        class="group bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-lg hover:shadow-black/5 transition-all cursor-pointer flex flex-col gap-4"
    >
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-0" style="direction: ltr">
                <img
                    v-for="(a, i) in kit.accounts.slice(0, 4)"
                    :key="i"
                    :src="a.avatar"
                    class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-900 object-cover -ml-2 first:ml-0"
                    :style="{ zIndex: 4 - i }"
                />
            </div>
            <span
                class="text-xs font-semibold text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-lg"
            >
                {{ kit.approved_accounts }} {{ $t('common.accounts') }}
            </span>
        </div>
        <div>
            <h3
                class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-primary transition-colors mb-1 break-all line-clamp-1"
            >
                {{ kit.title }}
            </h3>
            <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">{{ kit.description }}</p>
        </div>
        <div
            class="flex items-center justify-between mt-auto pt-3 border-t border-gray-50 dark:border-gray-800"
        >
            <span class="flex items-center gap-1.5 text-xs text-gray-400">
                <ArrowUpTrayIcon class="w-3.5 h-3.5" />

                {{ kit.uses }} {{ $t('common.uses') }}
            </span>
            <router-link :to="kit.path" class="text-xs font-bold text-primary hover:underline"
                >{{ $t('common.useKit') }} →</router-link
            >
        </div>
    </div>
</template>

<script setup>
import { ArrowUpTrayIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    kit: {
        type: Object,
        required: true
    }
})
</script>
