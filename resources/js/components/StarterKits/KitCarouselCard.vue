<template>
    <div
        class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-xl transition-all transform hover:-translate-y-1 cursor-pointer"
    >
        <div class="p-6 bg-gradient-to-br from-primary/90 to-[#FF4571]/85 dark:to-[#FF4571]/50">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3
                        class="font-extrabold text-xl text-white mb-1 tracking-tight group-hover:text-primary transition-colors mb-1 line-clamp-1"
                    >
                        {{ kit.title }}
                    </h3>
                    <p class="text-sm text-gray-100 line-clamp-1">
                        {{ kit.description }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm text-red-100 dark:text-red-200">
                <UsersIcon class="w-4 h-4" />
                {{ kit.total_accounts }} {{ $t('common.accounts') }}
                <span class="mx-1">•</span>
                <ArrowUpTrayIcon class="w-4 h-4" />

                {{ kit.uses }} {{ $t('common.uses') }}
            </div>
        </div>

        <div v-if="kit?.accounts?.length" class="p-6">
            <div class="flex -space-x-3 mb-4">
                <img
                    v-for="(account, idx) in kit.accounts.slice(0, 5)"
                    :key="idx"
                    :src="account.avatar"
                    class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-900 object-cover"
                />
                <div
                    v-if="kit.total_accounts > 5"
                    class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-900 bg-gradient-to-br from-primary to-[#FF4571] flex items-center justify-center text-white text-sm font-bold"
                >
                    +{{ kit.total_accounts - 5 }}
                </div>
            </div>

            <router-link
                :to="kit.path"
                class="flex justify-center w-full py-3 bg-gradient-to-r from-primary to-[#FF4571] hover:from-[#E91E63] hover:to-primary text-white font-semibold rounded-xl transition-all transform hover:scale-105"
            >
                {{ isMyKit ? $t('common.viewKit') : $t('common.useKit') }}
            </router-link>
        </div>

        <div v-else-if="isMyKit" class="p-6">
            <router-link
                :to="kit.path"
                class="flex justify-center w-full py-3 bg-gradient-to-r from-primary to-[#FF4571] hover:from-[#E91E63] hover:to-primary text-white font-semibold rounded-xl transition-all transform hover:scale-105"
            >
                {{ isMyKit ? $t('common.viewKit') : $t('common.useKit') }}
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ArrowUpTrayIcon, UsersIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    kit: {
        type: Object,
        required: true
    },
    isMyKit: {
        type: Boolean,
        default: false
    }
})
</script>
