<template>
    <MainLayout>
        <KitLoaderSkeleton v-if="loading" />

        <KitFeatureDisabled v-else-if="isDisabled && !loading" />

        <KitLoaderError v-else-if="error" :error="error" @retry="retryLoad" />

        <div v-else class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body lg:-mt-5 lg:-mr-2">
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
                                >{{ t('common.myStarterKits') }}</span
                            >
                        </div>
                        <h1
                            class="font-display text-4xl md:text-5xl font-bold text-gray-950 dark:text-white leading-tight tracking-tight"
                        >
                            {{ t('common.yourStarterKits') }}
                        </h1>
                    </div>
                    <div class="flex gap-3">
                        <AnimatedButton
                            :disabled="!canCreate"
                            @click="router.push('/starter-kits/create')"
                            variant="primaryGradient"
                            :pill="true"
                        >
                            <div class="flex items-center gap-2">
                                <PlusIcon class="w-4 h-4" />
                                {{ t('common.createKit') }}
                            </div>
                        </AnimatedButton>

                        <AnimatedButton
                            @click="router.push('/starter-kits/joined-kits')"
                            variant="light"
                            :pill="true"
                        >
                            {{ t('common.joinedKits') }}
                        </AnimatedButton>
                    </div>
                </div>

                <div
                    v-if="myKits.length > 0"
                    class="flex items-center justify-between flex-col lg:flex-row gap-6 mt-8 pt-8 border-t border-gray-200 dark:border-gray-800"
                >
                    <div class="flex items-center gap-6">
                        <div class="flex items-baseline gap-1.5">
                            <span
                                class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                                >{{ formatCount(stats.total_kits) }}</span
                            >
                            <span class="text-sm text-gray-400">{{ t('common.kits') }}</span>
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
                        <div class="w-px h-5 bg-gray-200 dark:bg-gray-800"></div>
                        <div class="flex items-baseline gap-1.5">
                            <span
                                class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                                >{{ formatCount(stats.total_uses) }}</span
                            >
                            <span class="text-sm text-gray-400">{{ t('common.totalUses') }}</span>
                        </div>
                    </div>

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
            </div>

            <div v-if="!loading && myKits.length === 0" class="max-w-7xl mx-auto px-6 md:px-12">
                <div
                    class="border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-3xl py-24 text-center"
                >
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center mx-auto mb-5"
                    >
                        <RectangleStackIcon class="w-7 h-7 text-primary" />
                    </div>
                    <h3 class="font-display text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ t('common.noKitsYet') }}
                    </h3>
                    <p
                        v-if="canCreate"
                        class="text-gray-400 text-sm mb-8 max-w-xs mx-auto leading-relaxed"
                    >
                        {{
                            t(
                                'common.createYourFirstStarterKitAndHelpNewUsersDiscoverGreatAccountsOnLoops'
                            )
                        }}.
                    </p>
                    <router-link
                        v-if="canCreate"
                        to="/starter-kits/create"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-white bg-primary hover:bg-[#D91B42] transition-all shadow-lg shadow-primary/25"
                    >
                        <PlusIcon class="w-4 h-4" />
                        {{ t('common.createYourFirstKit') }}
                    </router-link>
                </div>
            </div>

            <div v-else-if="!loading" class="max-w-7xl mx-auto px-6 md:px-12 pb-12">
                <div class="space-y-4">
                    <div
                        v-for="kit in myKits"
                        :key="kit.id"
                        class="group bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/30 transition-all"
                    >
                        <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center gap-5">
                            <div
                                class="flex-shrink-0 flex items-center relative"
                                style="width: 72px; height: 44px"
                            >
                                <img
                                    v-for="(a, i) in kit.accounts.slice(0, 3)"
                                    :key="i"
                                    :src="a.avatar"
                                    class="absolute top-0 w-11 h-11 rounded-full border-2 border-white dark:border-gray-900 object-cover shadow-sm"
                                    :style="{ left: i * 20 + 'px', zIndex: 3 - i }"
                                />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3
                                            class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors text-lg leading-tight truncate"
                                        >
                                            {{ kit.title }}
                                        </h3>
                                        <p class="text-sm text-gray-400 mt-1 line-clamp-1">
                                            {{ kit.description }}
                                        </p>
                                    </div>

                                    <span
                                        class="flex-shrink-0 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full"
                                        :class="
                                            kit.status === 10
                                                ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'
                                                : kit.status === 5
                                                  ? 'bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800'
                                                  : 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800'
                                        "
                                    >
                                        {{ kit.status_text }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-3">
                                    <span
                                        class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <UsersIcon class="w-3.5 h-3.5" />
                                        {{ kit.approved_accounts }} {{ t('common.accounts') }}
                                    </span>
                                    <span
                                        class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <ArrowUpTrayIcon class="w-3.5 h-3.5" />
                                        {{ formatCount(kit.uses) }} {{ t('common.uses') }}
                                    </span>
                                    <span
                                        class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <CalendarIcon class="w-3.5 h-3.5" />
                                        {{ t('common.updated') }}
                                        {{ formatRecentDate(kit.updated_at) }}
                                    </span>
                                    <div class="flex gap-1.5">
                                        <span
                                            v-for="tag in kit.hashtags.slice(0, 3)"
                                            :key="tag"
                                            class="text-[10px] font-medium px-2 py-0.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-full border border-gray-100 dark:border-gray-700"
                                            >#{{ tag }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-2 flex-shrink-0 md:opacity-0 md:group-hover:opacity-100 transition-opacity"
                            >
                                <router-link
                                    :to="kit.path"
                                    class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-gray-300 transition-all flex items-center gap-1.5"
                                >
                                    <EyeIcon class="w-3.5 h-3.5" />
                                    {{ t('studio.view') }}
                                </router-link>
                                <router-link
                                    :to="`${kit.path}/edit`"
                                    class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-gray-300 transition-all flex items-center gap-1.5"
                                >
                                    <PencilIcon class="w-3.5 h-3.5" />
                                    {{ t('common.edit') }}
                                </router-link>
                                <button
                                    @click="shareKit(kit)"
                                    class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-gray-300 transition-all flex items-center gap-1.5"
                                >
                                    <ShareIcon class="w-3.5 h-3.5" />
                                    {{ t('common.share') }}
                                </button>
                                <button
                                    @click="handleKitDelete(kit)"
                                    class="p-2 text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 transition-colors rounded-xl hover:bg-red-50 dark:hover:bg-red-950/30"
                                >
                                    <TrashIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <AnimatedButton
                        v-if="hasMore"
                        class="w-full"
                        :disabled="!hasMore || loadingMore"
                        @click="handleLoadMore"
                    >
                        {{ t('common.loadMore') }}
                    </AnimatedButton>
                </div>

                <KitDeleteConfirmModal
                    v-if="showDeleteModal"
                    v-model="showDeleteModal"
                    :confirm-word="kitToDelete?.title"
                    :title="`${t('common.deleteThisKit')}?`"
                    :description="`${t('common.andAllItsDataWillBePermanentlyRemoved')}.`"
                    :loading="deleting"
                    @confirm="deleteKit"
                />
            </div>

            <div class="h-40"></div>
        </div>
    </MainLayout>
</template>

<script setup>
import AnimatedButton from '@/components/AnimatedButton.vue'
import { useUtils } from '@/composables/useUtils'
import MainLayout from '@/layouts/MainLayout.vue'
import {
    ArrowUpTrayIcon,
    CalendarIcon,
    ChevronLeftIcon,
    EyeIcon,
    PencilIcon,
    PlusIcon,
    RectangleStackIcon,
    ShareIcon,
    TrashIcon,
    UsersIcon
} from '@heroicons/vue/24/outline'
import { ref, computed, onMounted, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import axios from '~/plugins/axios'
const api = axios.getAxiosInstance()

const appConfig = inject('appConfig')
const { formatRecentDate, formatCount } = useUtils()
const router = useRouter()
const loading = ref(true)
const kitToDelete = ref(null)
const config = ref()
const myKits = ref([])
const stats = ref()
const hasMore = ref(false)
const loadingMore = ref(false)
const cursorToken = ref()
const showDeleteModal = ref(false)
const deleting = ref(false)
const isDisabled = ref(false)
const { t } = useI18n()

const sortBy = ref('latest')
const sortOptions = [
    { label: t('profile.tabFilterOptions.Latest'), value: 'latest' },
    { label: t('profile.tabFilterOptions.Popular'), value: 'popular' },
    { label: t('profile.tabFilterOptions.Oldest'), value: 'oldest' }
]

const totalAccounts = computed(() => myKits.value.reduce((sum, k) => sum + k.accountCount, 0))
const totalUses = computed(() => myKits.value.reduce((sum, k) => sum + k.uses, 0))
const canCreate = ref(false)

const confirmDelete = (kit) => {
    kitToDelete.value = kit
}

const shareKit = (kit) => {
    if (navigator.share) {
        navigator.share({ title: kit.name, url: `${window.location.origin}${kit.path}` })
    } else {
        navigator.clipboard.writeText(`${window.location.origin}${kit.path}`)
    }
}

function handleSortChange(value) {
    if (sortBy.value === value) return
    sortBy.value = value
    fetchMyKits()
}

const fetchConfig = async () => {
    if (appConfig.starterKits === false) {
        isDisabled.value = true
        loading.value = false
        return
    }
    try {
        const res = await api.get('/api/v1/starter-kits/self/config')
        config.value = res.data.data
        canCreate.value = res.data.data.can_create
    } catch (e) {
    } finally {
        fetchMyKits()
    }
}

const fetchMyKits = async () => {
    try {
        const res = await api.get('/api/v1/starter-kits/my-kits', {
            params: { limit: 5, sort: sortBy.value }
        })
        myKits.value = res.data.data
        hasMore.value = res.data.meta.next_cursor
        cursorToken.value = res.data.meta.next_cursor
        stats.value = res.data.stats
    } catch (e) {
    } finally {
        loading.value = false
    }
}

const handleLoadMore = async () => {
    loadingMore.value = true
    try {
        const res = await api.get('/api/v1/starter-kits/my-kits', {
            params: { cursor: cursorToken.value, sort: sortBy.value, limit: 5 }
        })
        myKits.value = [...myKits.value, ...res.data.data]
        hasMore.value = res.data.meta.next_cursor
        cursorToken.value = res.data.meta.next_cursor
    } catch (e) {
    } finally {
        loadingMore.value = false
    }
}

const handleKitDelete = (kit) => {
    kitToDelete.value = kit
    showDeleteModal.value = true
}

const deleteKit = async () => {
    deleting.value = true
    try {
        await api.delete(`/api/v1/starter-kits/details/${kitToDelete.value.id}`)
    } catch (e) {
        deleting.value = false
        showDeleteModal.value = false
    } finally {
        kitToDelete.value = null
        deleting.value = false
        showDeleteModal.value = false
        fetchConfig()
    }
}

onMounted(() => {
    fetchConfig()
})
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
