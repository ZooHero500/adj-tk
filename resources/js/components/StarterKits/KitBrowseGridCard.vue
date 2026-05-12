<template>
    <router-link
        v-for="kit in filteredKits"
        :key="kit.id"
        :to="kit.path"
        class="group bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/30 transition-all cursor-pointer flex flex-col"
    >
        <div class="relative h-32 bg-gray-50 dark:bg-gray-800 flex-shrink-0">
            <img
                v-if="kit.header_url"
                :src="kit.header_url"
                class="w-full h-full object-cover"
                alt=""
            />

            <div v-else class="absolute inset-0 flex justify-center items-center px-5">
                <div class="flex items-center">
                    <img
                        v-for="(a, i) in kit.accounts.slice(0, 4)"
                        :key="i"
                        :src="a.avatar"
                        class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-800 object-cover -ml-2 first:ml-0 shadow-sm"
                        :style="{ zIndex: 4 - i }"
                    />
                </div>
            </div>

            <span
                class="absolute top-3 right-3 text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm px-2 py-1 rounded-lg flex items-center gap-1 shadow-sm"
            >
                <UsersIcon class="w-3 h-3" />
                {{ kit.approved_accounts }}
            </span>

            <div
                v-if="kit.header_url && kit.icon_url"
                class="absolute -bottom-5 left-5 w-10 h-10 rounded-xl overflow-hidden border-2 border-white dark:border-gray-900 shadow-md flex-shrink-0"
            >
                <img :src="kit.icon_url" class="w-full h-full object-cover" alt="" />
            </div>
        </div>

        <div
            class="flex flex-col gap-3 p-5 flex-1"
            :class="kit.header_url && kit.icon_url ? 'pt-7' : ''"
        >
            <div class="flex-1">
                <h3
                    class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors mb-1"
                >
                    {{ kit.title }}
                </h3>
                <p class="text-sm text-gray-400 line-clamp-1 leading-relaxed">
                    {{ kit.description }}
                </p>
            </div>

            <div class="flex gap-1.5 flex-wrap">
                <span
                    v-for="tag in kit.hashtags.slice(0, 3)"
                    :key="tag"
                    class="text-[10px] font-medium px-2 py-0.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-full border border-gray-100 dark:border-gray-700"
                    >#{{ tag }}</span
                >
            </div>

            <div
                class="flex items-center justify-between pt-3 border-t border-gray-50 dark:border-gray-800"
            >
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">{{ t('common.curatedBy') }}</span>
                    <img :src="kit.creator.avatar" class="hidden lg:block w-5 h-5 rounded-full" />
                    <span class="text-xs text-gray-400"
                        >@{{ textTruncate(kit.creator.username, 14) }}</span
                    >
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <ArrowUpTrayIcon class="w-3.5 h-3.5" />{{ formatCount(kit.uses) }}
                    </span>
                    <button
                        class="text-xs font-bold text-primary hover:underline flex items-center gap-1"
                    >
                        {{ t('common.useKit') }}
                        <ArrowRightIcon class="w-3 h-3" />
                    </button>
                </div>
            </div>
        </div>
    </router-link>
</template>

<script setup>
import { ArrowUpTrayIcon, ArrowRightIcon, UsersIcon } from '@heroicons/vue/24/outline'
import { useUtils } from '@/composables/useUtils'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    filteredKits: {
        type: Array,
        required: true
    }
})

const { t } = useI18n()

const { formatCount, textTruncate } = useUtils()
</script>
