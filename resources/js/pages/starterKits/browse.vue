<template>
    <MainLayout>
        <KitLoaderSkeleton v-if="loading" />

        <KitFeatureDisabled v-else-if="isDisabled && !loading" />

        <div
            v-else
            class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body lg:-mt-5 lg:-mr-2.5"
        >
            <div class="max-w-7xl mx-auto px-6 md:px-12 pt-14 pb-5">
                <div class="flex items-center gap-3 mb-6">
                    <router-link
                        to="/starter-kits"
                        class="text-sm text-gray-400 hover:text-gray-600 font-medium dark:hover:text-gray-200 transition-colors flex items-center gap-1.5"
                    >
                        <ChevronLeftIcon class="w-4 h-4" />
                        {{ t('common.starterKits') }}
                    </router-link>
                </div>

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2.5 mb-4">
                            <span class="block w-8 h-px bg-primary"></span>
                            <span
                                class="text-xs font-semibold tracking-[0.15em] uppercase text-primary"
                                >{{ t('common.browse') }}</span
                            >
                        </div>
                        <h1
                            class="font-display text-4xl md:text-5xl font-bold text-gray-950 dark:text-white leading-tight tracking-tight"
                        >
                            {{ t('common.starterKits') }}
                        </h1>

                        <div class="flex items-center gap-6 mt-3 pt-2">
                            <div class="flex items-baseline gap-1.5">
                                <span
                                    class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                                    >{{ formatCount(stats.total_kits) }}</span
                                >
                                <span class="text-sm text-gray-400">{{
                                    t('common.activeKits')
                                }}</span>
                            </div>
                            <div class="w-px h-5 bg-gray-200 dark:bg-gray-800"></div>
                            <div class="flex items-baseline gap-1.5">
                                <span
                                    class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                                    >{{ formatCount(stats.total_uses) }}</span
                                >
                                <span class="text-sm text-gray-400">{{
                                    t('common.timesUsed')
                                }}</span>
                            </div>
                            <div class="w-px h-5 bg-gray-200 dark:bg-gray-800"></div>
                            <div class="flex items-baseline gap-1.5">
                                <span
                                    class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                                    >{{ formatCount(stats.total_accounts) }}</span
                                >
                                <span class="text-sm text-gray-400">{{
                                    t('common.accountsCurated')
                                }}</span>
                            </div>
                        </div>
                    </div>
                    <router-link
                        v-if="authStore.isAuthenticated"
                        to="/starter-kits/create"
                        class="self-start md:self-auto px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary hover:bg-[#D91B42] transition-all shadow-md shadow-primary/25 flex items-center gap-2 flex-shrink-0"
                    >
                        <PlusIcon class="w-4 h-4" />
                        {{ t('common.createKit') }}
                    </router-link>
                </div>
            </div>

            <div
                class="sticky top-0 z-20 bg-[#FAFAFA]/90 dark:bg-[#0A0A0A]/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800"
            >
                <div class="max-w-7xl mx-auto px-6 md:px-12 py-3 flex flex-col gap-3">
                    <div class="relative w-full sm:max-w-sm">
                        <MagnifyingGlassIcon
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search kits..."
                            class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 text-gray-900 dark:text-white placeholder-gray-400 transition-all"
                        />
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 font-medium hidden sm:block"
                                >{{ t('common.sort') }}:</span
                            >
                            <div
                                class="flex gap-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-1"
                            >
                                <button
                                    v-for="opt in sortOptions"
                                    :key="opt.value"
                                    @click="sortBy = opt.value"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                                    :class="
                                        sortBy === opt.value
                                            ? 'bg-primary text-white shadow-sm'
                                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                                    "
                                >
                                    {{ opt.label }}
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 font-medium hidden sm:block"
                                >{{ t('common.limit') }}:</span
                            >
                            <div
                                class="flex gap-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-1"
                            >
                                <button
                                    v-for="opt in limitOptions"
                                    :key="opt.value"
                                    @click="limitBy = opt.value"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                                    :class="
                                        limitBy === opt.value
                                            ? 'bg-primary text-white shadow-sm'
                                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                                    "
                                >
                                    {{ opt.label }}
                                </button>
                            </div>
                        </div>

                        <div
                            class="sm:ml-auto flex gap-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-1"
                        >
                            <button
                                @click="view = 'grid'"
                                class="p-1.5 rounded-lg transition-all"
                                :class="
                                    view === 'grid'
                                        ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white'
                                        : 'text-gray-400 hover:text-gray-600'
                                "
                            >
                                <Squares2X2Icon class="w-4 h-4" />
                            </button>
                            <button
                                @click="view = 'list'"
                                class="p-1.5 rounded-lg transition-all"
                                :class="
                                    view === 'list'
                                        ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white'
                                        : 'text-gray-400 hover:text-gray-600'
                                "
                            >
                                <ListBulletIcon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="max-w-7xl mx-auto px-6 md:px-12 pb-3 flex gap-2 overflow-x-auto scrollbar-hide"
                >
                    <button
                        @click="activeTag = null"
                        class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold transition-all"
                        :class="
                            activeTag === null
                                ? 'bg-primary text-white'
                                : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 hover:border-gray-300'
                        "
                    >
                        All
                    </button>
                    <button
                        v-for="tag in allTags"
                        :key="tag"
                        @click="activeTag = tag"
                        class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold transition-all"
                        :class="
                            activeTag === tag
                                ? 'bg-primary text-white'
                                : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 hover:border-primary/40 hover:text-primary'
                        "
                    >
                        #{{ tag }}
                    </button>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 md:px-12 py-8">
                <div v-if="filteredKits.length === 0" class="py-24 text-center">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4"
                    >
                        <MagnifyingGlassIcon class="w-7 h-7 text-gray-400" />
                    </div>
                    <h3 class="font-display font-bold text-gray-900 dark:text-white mb-2">
                        {{ t('common.noKitsFound') }}
                    </h3>
                    <p class="text-sm text-gray-400">
                        {{ t('common.tryADifferentSearchOrTagFilter') }}
                    </p>
                </div>

                <KitGridSkeleton
                    v-else-if="isFetching"
                    :view="view"
                    :count="view === 'grid' ? 9 : 6"
                />

                <div
                    v-else-if="view === 'grid'"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <KitBrowseGridCard :filtered-kits="filteredKits" />
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="kit in filteredKits"
                        :key="kit.id"
                        class="group bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-lg hover:shadow-black/5 transition-all cursor-pointer"
                    >
                        <div class="flex">
                            <div class="flex items-center gap-5 p-5 flex-1 min-w-0">
                                <div
                                    class="flex-shrink-0 relative"
                                    style="width: 58px; height: 46px"
                                >
                                    <div
                                        v-if="kit.icon_url"
                                        class="absolute top-0 left-0 w-12 h-12 rounded-xl overflow-hidden border-2 border-white dark:border-gray-900 shadow-sm"
                                    >
                                        <img
                                            :src="kit.icon_url"
                                            class="w-full h-full object-cover"
                                            :alt="`${kit.title}'s Starter Kit Icon`"
                                        />
                                    </div>
                                    <template v-else>
                                        <img
                                            v-for="(a, i) in kit.accounts.slice(0, 3)"
                                            :key="i"
                                            :src="a.avatar"
                                            class="absolute top-0 w-10 h-10 rounded-full border-2 border-white dark:border-gray-900 object-cover shadow-sm"
                                            :style="{ left: i * 16 + 'px', zIndex: 3 - i }"
                                        />
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <h3
                                                class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors truncate"
                                            >
                                                {{ kit.title }}
                                            </h3>
                                            <p class="text-sm text-gray-400 truncate mt-0.5">
                                                {{ kit.description }}
                                            </p>
                                        </div>
                                        <router-link
                                            :to="kit.path"
                                            class="flex-shrink-0 px-4 py-1.5 text-xs font-bold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 group-hover:bg-primary group-hover:border-primary group-hover:text-white transition-all"
                                        >
                                            {{ t('common.useKit') }}
                                        </router-link>
                                    </div>
                                    <div class="flex items-center gap-4 mt-2.5">
                                        <div class="flex items-center gap-1.5">
                                            <img
                                                :src="kit.creator.avatar"
                                                class="w-4 h-4 rounded-full"
                                            />
                                            <span class="text-xs text-gray-400"
                                                >@{{ kit.creator.username }}</span
                                            >
                                        </div>
                                        <div class="flex gap-1.5">
                                            <span
                                                v-for="tag in kit.hashtags.slice(0, 2)"
                                                :key="tag"
                                                class="text-[10px] font-medium px-2 py-0.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-full"
                                                >#{{ tag }}</span
                                            >
                                        </div>
                                        <div
                                            class="ml-auto flex items-center gap-3 text-xs text-gray-400"
                                        >
                                            <span class="flex items-center gap-1">
                                                <UsersIcon class="w-3.5 h-3.5" />{{
                                                    kit.approved_accounts
                                                }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <ArrowUpTrayIcon class="w-3.5 h-3.5" />{{
                                                    formatCount(kit.uses)
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="filteredKits.length" class="mt-8 flex items-center justify-center gap-2">
                    <button
                        v-if="hasPrevPage"
                        @click="goPrev"
                        :disabled="loading"
                        class="cursor-pointer px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-gray-300 transition-all flex items-center gap-1.5 disabled:opacity-50"
                    >
                        <ChevronLeftIcon class="w-4 h-4" /> {{ t('common.previous') }}
                    </button>
                    <button
                        v-if="hasNextPage"
                        @click="goNext"
                        :disabled="loading"
                        class="cursor-pointer px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-gray-300 transition-all flex items-center gap-1.5 disabled:opacity-50"
                    >
                        {{ t('common.next') }}
                        <ChevronRightIcon class="w-4 h-4" />
                    </button>
                </div>
                <div class="h-50"></div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import KitLoaderSkeleton from '@/components/StarterKits/KitLoaderSkeleton.vue'
import { useUtils } from '@/composables/useUtils'
import MainLayout from '@/layouts/MainLayout.vue'
import {
    ArrowUpTrayIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ListBulletIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    Squares2X2Icon,
    UsersIcon
} from '@heroicons/vue/24/outline'
import { useI18n } from 'vue-i18n'
import { ArrowRightIcon } from '@heroicons/vue/24/solid'
import { ref, computed, onMounted, watch, inject } from 'vue'
import axios from '~/plugins/axios'
import KitBrowseGridCard from '@/components/StarterKits/KitBrowseGridCard.vue'
const api = axios.getAxiosInstance()

const authStore = inject('authStore')
const appConfig = inject('appConfig')

const search = ref('')
const sortBy = ref('popular')
const limitBy = ref(6)
const view = ref('grid')
const activeTag = ref(null)
const stats = ref({ total_kits: 0, total_uses: 0, total_accounts: 0 })
const loading = ref(true)
const isDisabled = ref(false)
const isFetching = ref(false)
const { t } = useI18n()

const nextCursor = ref(null)
const prevCursor = ref(null)
const cursorHistory = ref([])
const currentCursor = ref(null)
const hasNextPage = computed(() => !!nextCursor.value)
const hasPrevPage = computed(() => cursorHistory.value.length > 0)

const { formatCount } = useUtils()

const sortOptions = [
    { label: t('profile.tabFilterOptions.Popular'), value: 'popular' },
    { label: t('profile.tabFilterOptions.Latest'), value: 'latest' },
    { label: t('profile.tabFilterOptions.Oldest'), value: 'oldest' }
]

const limitOptions = [
    { label: '3', value: 3 },
    { label: '6', value: 6 },
    { label: '9', value: 9 },
    { label: '12', value: 12 }
]

const allTags = ref([])
const allKits = ref([])

const filteredKits = computed(() => allKits.value)

let searchDebounceTimer = null

const resetCursors = () => {
    currentCursor.value = null
    nextCursor.value = null
    prevCursor.value = null
    cursorHistory.value = []
}

const fetchKits = async () => {
    if (appConfig.starterKits === false) {
        isDisabled.value = true
        loading.value = false
        return
    }

    if (loading.value === false) {
        isFetching.value = true
    }

    try {
        const res = await api.get('/api/v1/starter-kits/browse', {
            params: {
                sort: sortBy.value,
                tag: activeTag.value,
                q: search.value,
                cursor: currentCursor.value,
                limit: limitBy.value
            }
        })

        allKits.value = res.data.data

        nextCursor.value = res.data.meta.next_cursor ?? null
        prevCursor.value = res.data.meta.prev_cursor ?? null

        if (res.data.stats) {
            stats.value = res.data.stats
        }
        if (res.data.hashtags) {
            allTags.value = res.data.hashtags.map((t) => t.name)
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
        isFetching.value = false
    }
}

const goNext = () => {
    if (!nextCursor.value) return
    cursorHistory.value.push(currentCursor.value)
    currentCursor.value = nextCursor.value
    fetchKits()
}

const goPrev = () => {
    if (!cursorHistory.value.length) return
    currentCursor.value = cursorHistory.value.pop()
    fetchKits()
}

watch([sortBy, activeTag, limitBy], () => {
    resetCursors()
    fetchKits()
})

watch(search, () => {
    clearTimeout(searchDebounceTimer)
    searchDebounceTimer = setTimeout(() => {
        resetCursors()
        fetchKits()
    }, 350)
})

onMounted(fetchKits)
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
