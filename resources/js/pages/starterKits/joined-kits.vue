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
                                >{{ t('common.joinedStarterKits') }}</span
                            >
                        </div>
                        <h1
                            class="font-display text-4xl md:text-5xl font-bold text-gray-950 dark:text-white leading-tight tracking-tight"
                        >
                            {{ t('common.joinedKits') }}
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
                            @click="router.push('/starter-kits/my-kits')"
                            variant="light"
                            :pill="true"
                        >
                            {{ t('common.myKits') }}
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
                                >{{ formatCount(stats.total_joined) }}</span
                            >
                            <span class="text-sm text-gray-400">{{ t('common.joinedKits') }}</span>
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
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-3">
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
                                        class="flex-shrink-0 text-[10px] font-bold uppercase tracking-wider px-2.5 rounded-full"
                                        :class="
                                            kit.status_text === 'Active'
                                                ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'
                                                : 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800'
                                        "
                                    >
                                        {{ kit.status_text }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 flex-shrink-0">
                                <Dropdown menu-label="Starter kit options" align="right">
                                    <template #trigger="{ open }">
                                        <AnimatedButton variant="light" :pill="true">
                                            <div class="flex items-center gap-2">
                                                <Bars3Icon class="w-4 h-4" />
                                                <span class="inline">Options</span>
                                                <ChevronDownIcon
                                                    class="w-3.5 h-3.5 transition-transform duration-200"
                                                    :class="{ 'rotate-180': open }"
                                                />
                                            </div>
                                        </AnimatedButton>
                                    </template>

                                    <DropdownItem :to="kit.path">
                                        <EyeIcon class="w-3.5 h-3.5" />

                                        {{ t('common.viewKit') }}
                                    </DropdownItem>

                                    <DropdownDivider />

                                    <DropdownItem v-if="kit.is_owner" :to="`${kit.path}/edit`">
                                        <PencilIcon class="w-3.5 h-3.5" />
                                        {{ t('common.editKit') }}
                                    </DropdownItem>

                                    <DropdownDivider v-if="kit.is_owner" />

                                    <DropdownItem
                                        :destructive="true"
                                        @click="handleConfirmRemove(kit)"
                                    >
                                        <ExclamationTriangleIcon class="w-3.5 h-3.5" />
                                        {{ t('common.leaveKit') }}
                                    </DropdownItem>
                                </Dropdown>
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
    ChevronDownIcon,
    ChevronLeftIcon,
    ExclamationTriangleIcon,
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
import { useAlertModal } from '@/composables/useAlertModal.js'
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

const { alertModal, confirmModal } = useAlertModal()

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
        const res = await api.get('/api/v1/starter-kits/joined-kits', {
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

const handleConfirmRemove = async (kit) => {
    const result = await confirmModal(
        'Confirm Leave Starter Kit',
        `Are you sure you want to leave the <strong>${kit.title}</strong> Starter Kit created by <strong>@${kit.creator?.username}</strong>?<br /><br />This action is permanent and cannot be undone.`,
        t('common.leaveKit'),
        t('common.cancel')
    )

    if (result) {
        try {
            await api.post(`/api/v1/starter-kits/details/${kit.id}/membership/revoke`)
        } catch (e) {
        } finally {
            fetchConfig()
        }
    }
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
