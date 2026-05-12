<template>
    <div class="w-full border-b border-gray-200 dark:border-neutral-700">
        <div class="flex justify-between items-center">
            <div class="flex">
                <button
                    v-for="tab in mainTabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    :class="[
                        'px-4 lg:px-10 py-4 text-xs lg:text-[17px] font-semibold transition-colors duration-200 relative cursor-pointer flex items-center gap-1 lg:gap-1.5',
                        activeTab === tab.key
                            ? 'text-black dark:text-white'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                    ]"
                >
                    <div
                        v-if="tab.key === 'bookmarks'"
                        class="hidden lg:relative lg:inline-block w-5 lg:w-6 h-5 lg:h-6"
                    >
                        <BookmarkIcon class="w-full h-full" />

                        <div
                            class="absolute top-1/2 -right-0 -translate-y-1/2 flex items-center justify-center bg-white dark:bg-gray-900 rounded-full ring-1 ring-white dark:ring-gray-900"
                        >
                            <EyeSlashIcon class="w-2.5 h-2.5" />
                        </div>
                    </div>
                    {{ $t(`profile.tabOptions.${tab.label}`) }}

                    <div
                        v-if="activeTab === tab.key"
                        class="absolute bottom-0 left-0 right-0 h-0.5 bg-black dark:bg-white transition-all duration-200"
                    />
                </button>
            </div>

            <div v-if="activeTab === 'videos'" class="flex items-center lg:pr-4">
                <div class="flex bg-gray-100 dark:bg-neutral-800 rounded-lg p-1">
                    <button
                        v-for="filter in filterOptions"
                        :key="filter"
                        @click="activeFilter = filter"
                        :class="[
                            'px-3 py-1.5 text-[10px] xl:text-[13px] font-medium rounded-md transition-all duration-200 relative cursor-pointer',
                            filter === 'Oldest' ? 'hidden lg:block' : '',
                            activeFilter === filter
                                ? 'bg-white dark:bg-neutral-700 text-black dark:text-white shadow-sm'
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                        ]"
                    >
                        {{ $t(`profile.tabFilterOptions.${filter}`) }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { BookmarkIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'
import { ref, computed } from 'vue'

const props = defineProps({
    showPrivateTabs: {
        type: Boolean,
        default: false
    }
})

const activeTab = ref('videos')
const activeFilter = ref('Latest')

const filterOptions = ['Latest', 'Popular', 'Oldest']

const mainTabs = computed(() => {
    const baseTabs = [
        { key: 'videos', label: 'Videos', icon: null }
        // { key: 'reposts', label: 'Reposts', icon: null },
    ]

    if (props.showPrivateTabs) {
        baseTabs.push({ key: 'bookmarks', label: 'Favourites', icon: null })
        // baseTabs.push({ key: 'comments', label: 'Comments', icon: null })
    }

    return baseTabs
})

const emit = defineEmits(['tab-change', 'filter-change'])

import { watch } from 'vue'

watch(activeTab, (newTab) => {
    emit('tab-change', newTab)
})

watch(activeFilter, (newFilter) => {
    emit('filter-change', newFilter)
})

defineExpose({
    activeTab,
    activeFilter
})
</script>
