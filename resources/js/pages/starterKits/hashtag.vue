<template>
    <MainLayout>
        <KitLoaderSkeleton v-if="loading && allKits.length === 0" />

        <KitFeatureDisabled v-else-if="isDisabled && !loading" />

        <KitLoaderError v-else-if="error" :error="error" @retry="retryLoad" />

        <div
            v-else
            class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body lg:-mt-5 lg:-mr-2.5"
        >
            <div class="max-w-7xl mx-auto px-6 md:px-12 pt-14 pb-10">
                <div class="flex items-center gap-3 mb-6">
                    <router-link
                        to="/starter-kits"
                        class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors flex items-center gap-1.5"
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
                                >{{ t('common.tag') }}</span
                            >
                        </div>
                        <h1
                            class="font-display text-4xl md:text-5xl font-bold text-gray-950 dark:text-white leading-tight tracking-tight flex items-baseline gap-1"
                        >
                            <span class="text-primary">#</span>{{ tag }}
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">
                            <span class="font-semibold text-gray-900 dark:text-white">{{
                                allKits.length
                            }}</span>
                            {{ t('common.kit') }}{{ allKits.length !== 1 ? 's' : '' }}
                            {{ t('common.taggedWith') }}
                            <span class="text-primary font-medium">#{{ tag }}</span>
                        </p>
                    </div>

                    <router-link
                        to="/starter-kits/create"
                        class="self-start md:self-auto px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary hover:bg-[#D91B42] transition-all shadow-md shadow-primary/25 flex items-center gap-2 flex-shrink-0"
                    >
                        <PlusIcon class="w-4 h-4" />
                        {{ t('common.createKit') }}
                    </router-link>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 md:px-12 mb-8">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                        >{{ t('common.related') }}:</span
                    >
                    <router-link
                        v-for="relatedTag in relatedTags"
                        :key="relatedTag"
                        :to="`/starter-kits/hashtag/${relatedTag}`"
                        class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 hover:border-primary/40 hover:text-primary transition-all"
                    >
                        #{{ relatedTag }}
                    </router-link>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 md:px-12 pb-12">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2">
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ t('common.showing') }} {{ allKits.length }}
                                {{ t('common.kits') }}
                            </span>
                            <div
                                class="flex gap-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-1"
                            >
                                <button
                                    v-for="opt in sortOptions"
                                    :key="opt.value"
                                    @click="handleSortChange(opt.value)"
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

                        <div
                            v-if="allKits.length === 0 && !loading"
                            class="py-24 text-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-3xl"
                        >
                            <div
                                class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4"
                            >
                                <HashtagIcon class="w-6 h-6 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-gray-900 dark:text-white mb-2">
                                {{ t('common.noKitsYetFor') }} #{{ tag }}
                            </h3>
                            <p class="text-sm text-gray-400 mb-6 max-w-xs mx-auto">
                                {{ t('common.beTheFirstToCreateAStarterKitWithThisTag') }}.
                            </p>
                            <router-link
                                :to="`/starter-kits/create?tag=${tag}`"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-primary hover:bg-[#D91B42] transition-all"
                            >
                                <PlusIcon class="w-4 h-4" />
                                {{ t('common.create') }} #{{ tag }} {{ t('common.kit') }}
                            </router-link>
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="kit in allKits"
                                :key="kit.id"
                                class="group bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/30 transition-all cursor-pointer"
                            >
                                <div class="flex items-center gap-5">
                                    <div
                                        class="flex-shrink-0 relative"
                                        style="width: 62px; height: 38px"
                                    >
                                        <img
                                            v-for="(a, i) in kit.previewAccounts.slice(0, 3)"
                                            :key="i"
                                            :src="a.avatar"
                                            class="absolute top-0 w-9 h-9 rounded-full border-2 border-white dark:border-gray-900 object-cover shadow-sm"
                                            :style="{ left: i * 18 + 'px', zIndex: 3 - i }"
                                        />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <h3
                                                    class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors truncate"
                                                >
                                                    {{ kit.name }}
                                                </h3>
                                                <p
                                                    class="text-sm text-gray-400 mt-0.5 line-clamp-1"
                                                >
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

                                        <div class="flex items-center gap-4 mt-2.5 flex-wrap">
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
                                                    v-for="t in kit.hashtags.slice(0, 3)"
                                                    :key="t"
                                                    class="text-[10px] font-medium px-2 py-0.5 rounded-full border transition-colors"
                                                    :class="
                                                        t === tag
                                                            ? 'bg-primary/10 text-primary border-primary/20'
                                                            : 'bg-gray-50 dark:bg-gray-800 text-gray-400 border-gray-100 dark:border-gray-700'
                                                    "
                                                    >#{{ t }}</span
                                                >
                                            </div>
                                            <div
                                                class="ml-auto flex items-center gap-3 text-xs text-gray-400"
                                            >
                                                <span class="flex items-center gap-1">
                                                    <UsersIcon class="w-3.5 h-3.5" />{{
                                                        kit.accountCount
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

                            <template v-if="loading">
                                <div
                                    v-for="n in 3"
                                    :key="`skel-${n}`"
                                    class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 animate-pulse"
                                >
                                    <div class="flex items-center gap-5">
                                        <div
                                            class="flex-shrink-0 w-[62px] h-9 bg-gray-100 dark:bg-gray-800 rounded-full"
                                        />
                                        <div class="flex-1 space-y-2">
                                            <div
                                                class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-1/2"
                                            />
                                            <div
                                                class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-3/4"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button
                            v-if="nextCursor && !loading"
                            @click="loadMore"
                            class="w-full mt-4 py-3 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 border border-dashed border-gray-200 dark:border-gray-800 rounded-xl hover:border-gray-300 dark:hover:border-gray-700 transition-all"
                        >
                            {{ t('common.loadMore') }}
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                        >
                            <h3
                                class="font-display font-bold text-gray-900 dark:text-white mb-4 text-sm uppercase tracking-wider"
                            >
                                {{ t('common.browseByTag') }}
                            </h3>
                            <div class="flex flex-col gap-1">
                                <router-link
                                    v-for="t in allTagsWithCount"
                                    :key="t.name"
                                    :to="`/starter-kits/hashtag/${t.name}`"
                                    class="flex items-center justify-between px-3 py-2 rounded-xl transition-all group/tag"
                                    :class="
                                        t.name === tag
                                            ? 'bg-primary/10 text-primary'
                                            : 'hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300'
                                    "
                                >
                                    <span class="text-sm font-medium">#{{ t.name }}</span>
                                    <span
                                        class="text-xs font-bold px-1.5 py-0.5 rounded-md"
                                        :class="
                                            t.name === tag
                                                ? 'bg-primary/20 text-primary'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                                        "
                                        >{{ t.count }}</span
                                    >
                                </router-link>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                        >
                            <h3
                                class="font-display font-bold text-gray-900 dark:text-white mb-4 text-sm uppercase tracking-wider"
                            >
                                {{ t('common.top') }} #{{ tag }} {{ t('common.curators') }}
                            </h3>
                            <div class="space-y-3">
                                <div
                                    v-for="(creator, idx) in topCreators"
                                    :key="creator.id"
                                    class="flex items-center gap-3 group cursor-pointer"
                                >
                                    <span
                                        class="text-xs font-bold w-5 text-gray-300 dark:text-gray-600 text-center"
                                        >{{ idx + 1 }}</span
                                    >
                                    <img
                                        :src="creator.avatar"
                                        class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-primary transition-colors"
                                        >
                                            {{ creator.display_name }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ creator.kitsCreated }}
                                            {{
                                                creator.kitsCreated == 1
                                                    ? t('common.kit')
                                                    : t('common.kits')
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl p-5 bg-gray-950 dark:bg-white text-white dark:text-gray-950"
                        >
                            <div
                                class="inline-flex items-center gap-1 text-xs font-bold bg-white/10 dark:bg-black/10 px-2.5 py-1 rounded-full mb-3"
                            >
                                <HashtagIcon class="w-3 h-3" />{{ tag }}
                            </div>
                            <h4 class="font-display text-base font-bold leading-snug mb-1">
                                {{ t('common.knowGreat') }} #{{ tag }} {{ t('common.creators') }}?
                            </h4>
                            <p class="text-xs opacity-60 mb-4 leading-relaxed">
                                {{ t('common.curateAKitAndHelpOthersDiscoverThem') }}.
                            </p>
                            <router-link
                                :to="`/starter-kits/create?tag=${tag}`"
                                class="block w-full py-2.5 text-sm font-bold text-center rounded-xl bg-primary text-white hover:bg-[#D91B42] transition-all"
                            >
                                {{ t('common.create') }} #{{ tag }} {{ t('common.kit') }}
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue'
import { useUtils } from '@/composables/useUtils'
import {
    ArrowUpTrayIcon,
    ChevronLeftIcon,
    HashtagIcon,
    PlusIcon,
    UsersIcon
} from '@heroicons/vue/24/outline'
import { ref, computed, onMounted, watch, inject } from 'vue'
import { useRoute } from 'vue-router'
import axios from '~/plugins/axios'
import { useI18n } from 'vue-i18n'

const api = axios.getAxiosInstance()
const { formatCount } = useUtils()
const route = useRoute()
const appConfig = inject('appConfig')
const { t } = useI18n()

const tag = computed(() => route.params.tag)
const sortBy = ref('popular')
const sortOptions = [
    { label: 'Popular', value: 'popular' },
    { label: 'Latest', value: 'latest' },
    { label: 'Oldest', value: 'oldest' }
]

const allKits = ref([])
const nextCursor = ref(null)
const loading = ref(true)
const error = ref(null)
const isDisabled = ref(false)

function mapKit(item) {
    return {
        id: item.id,
        name: item.title,
        description: item.description,
        uses: item.uses ?? 0,
        path: item.path,
        accountCount: item.approved_accounts ?? item.total_accounts ?? 0,
        hashtags: item.hashtags ?? [],
        creator: {
            username: item.creator?.username ?? '',
            avatar: item.creator?.avatar ?? '',
            display_name: item.creator?.name ?? item.creator?.username ?? ''
        },
        previewAccounts: (item.accounts?.length ? item.accounts : [item.creator]).filter(Boolean)
    }
}

async function fetchKits({ cursor = null, append = false } = {}) {
    if (appConfig.starterKits === false) {
        isDisabled.value = true
        loading.value = false
        return
    }

    loading.value = true
    error.value = null
    try {
        const params = { tag: tag.value, sort: sortBy.value }
        if (cursor) params.cursor = cursor

        const res = await api.get('/api/v1/starter-kits/hashtag/kits', { params })
        const mapped = (res.data.data ?? []).map(mapKit)

        allKits.value = append ? [...allKits.value, ...mapped] : mapped
        nextCursor.value = res.data.meta?.next_cursor ?? null
    } catch (err) {
        error.value = err
    } finally {
        loading.value = false
    }
}

function loadMore() {
    if (nextCursor.value && !loading.value) {
        fetchKits({ cursor: nextCursor.value, append: true })
    }
}

function handleSortChange(value) {
    if (sortBy.value === value) return
    sortBy.value = value
    fetchKits()
}

function retryLoad() {
    fetchKits()
}

const allTagsWithCount = computed(() => {
    const tagMap = {}
    allKits.value.forEach((k) =>
        k.hashtags.forEach((t) => {
            tagMap[t] = (tagMap[t] || 0) + 1
        })
    )
    return Object.entries(tagMap)
        .map(([name, count]) => ({ name, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 12)
})

const relatedTags = computed(() => {
    const related = new Set()
    allKits.value.forEach((k) =>
        k.hashtags.forEach((t) => {
            if (t !== tag.value) related.add(t)
        })
    )
    return [...related].slice(0, 6)
})

const topCreators = computed(() => {
    const map = {}
    allKits.value.forEach((k) => {
        const key = k.creator.username
        if (!map[key]) map[key] = { ...k.creator, id: key, kitsCreated: 0 }
        map[key].kitsCreated++
    })
    return Object.values(map)
        .sort((a, b) => b.kitsCreated - a.kitsCreated)
        .slice(0, 4)
})

onMounted(() => fetchKits())
watch(tag, () => fetchKits())
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
