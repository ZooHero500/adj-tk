<template>
    <div
        class="group flex items-center gap-4 p-3.5 bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-md hover:shadow-black/5 transition-all"
    >
        <span
            class="hidden sm:block text-xs font-semibold text-gray-300 dark:text-gray-700 w-5 text-center flex-shrink-0 tabular-nums"
        >
            {{ index + 1 }}
        </span>

        <img
            :src="account.avatar"
            :alt="account.name"
            class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-100 dark:ring-gray-800"
        />

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p
                    class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight"
                >
                    {{ account.name }}
                </p>

                <template v-if="isOwner">
                    <span
                        v-if="account.kit_status === 0"
                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 rounded-full text-[10px] font-bold"
                    >
                        <ClockIcon class="w-3 h-3" /> {{ $t('common.pending') }}
                    </span>

                    <span
                        v-else-if="account.kit_status === 2"
                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/60 rounded-full text-[10px] font-bold"
                    >
                        <XCircleIcon class="w-3 h-3" /> {{ $t('common.rejected') }}
                    </span>
                </template>
            </div>

            <p class="text-xs text-gray-400 dark:text-gray-500 leading-tight mt-0.5 truncate">
                @{{ account.username }}
            </p>
        </div>

        <p
            class="hidden xl:block text-xs text-gray-400 dark:text-gray-500 truncate flex-1 max-w-[200px]"
        >
            {{ account.bio?.trim() || '' }}
        </p>

        <div class="hidden md:flex items-center gap-4 flex-shrink-0 text-xs text-gray-400">
            <span class="flex items-center gap-1.5">
                <UsersIcon class="w-3.5 h-3.5" />
                {{ formatCount(account.follower_count) }}
            </span>
            <span class="flex items-center gap-1.5">
                <VideoCameraIcon class="w-3.5 h-3.5" />
                {{ formatCount(account.post_count) }}
            </span>
        </div>

        <a
            :href="account.url"
            target="_blank"
            class="flex-shrink-0 px-3 py-1.5 text-xs font-bold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 opacity-0 group-hover:opacity-100 group-hover:border-primary group-hover:text-primary transition-all"
        >
            {{ $t('studio.view') }} →
        </a>
    </div>
</template>

<script setup>
import { ClockIcon, XCircleIcon, UsersIcon, VideoCameraIcon } from '@heroicons/vue/24/outline'
import { useUtils } from '@/composables/useUtils'

const props = defineProps({
    account: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        required: true
    },
    isOwner: {
        type: Boolean,
        default: false
    }
})

const { formatCount } = useUtils()
</script>
