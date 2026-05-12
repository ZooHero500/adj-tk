<template>
    <div>
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Pending Changes
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Manage user Starter Kit updates
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400 dark:text-gray-500">
                        {{ meta.total }} {{ meta.total === 1 ? 'result' : 'results' }}
                    </span>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/60 rounded-xl overflow-hidden"
        >
            <div
                class="flex items-center gap-0 border-b border-gray-200 dark:border-gray-700/60 px-2"
            >
                <button
                    v-for="f in FILTERS"
                    :key="f.key"
                    @click="activeFilter = f.key"
                    class="relative flex items-center gap-2 text-sm px-4 py-3.5 transition-colors"
                    :class="
                        activeFilter === f.key
                            ? 'text-gray-900 dark:text-white font-medium'
                            : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'
                    "
                >
                    <component
                        :is="f.icon"
                        class="w-4 h-4"
                        :class="activeFilter === f.key ? f.activeColor : ''"
                    />
                    {{ f.label }}
                    <span
                        v-if="activeFilter === f.key"
                        class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-t-full"
                    />
                </button>
            </div>

            <div v-if="loading" class="divide-y divide-gray-100 dark:divide-gray-700/50">
                <div
                    v-for="i in 5"
                    :key="i"
                    class="flex items-center gap-4 px-5 py-4 animate-pulse"
                >
                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700/50 shrink-0" />
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 w-40 bg-gray-100 dark:bg-gray-700/50 rounded-full" />
                        <div class="h-3 w-24 bg-gray-100 dark:bg-gray-700/50 rounded-full" />
                    </div>
                    <div class="h-3 w-16 bg-gray-100 dark:bg-gray-700/50 rounded-full" />
                </div>
            </div>

            <div
                v-else-if="!changesets.length"
                class="flex flex-col items-center justify-center py-20 text-center"
            >
                <div
                    class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center mb-4"
                >
                    <SquaresPlusIcon class="w-5 h-5 text-gray-300 dark:text-gray-600" />
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    No {{ activeFilter.replace('_', ' ') }} changesets
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">
                    Check back later or switch filters above
                </p>
            </div>

            <div v-else class="divide-y divide-gray-100 dark:divide-gray-700/50">
                <a
                    v-for="cs in changesets"
                    :key="cs.id"
                    :href="`/admin/starter-kits-review/${cs.id}`"
                    class="group flex items-center gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors no-underline"
                >
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shrink-0 flex items-center justify-center"
                    >
                        <img
                            v-if="cs.starter_kit.icon_url"
                            :src="cs.starter_kit.icon_url"
                            class="w-full h-full object-cover"
                        />
                        <span
                            v-else
                            class="text-sm font-semibold text-gray-400 dark:text-gray-600 font-['Syne',sans-serif]"
                        >
                            {{ cs.starter_kit.title?.charAt(0)?.toUpperCase() }}
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                            >
                                {{ cs.starter_kit.title }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full shrink-0"
                                :class="statusStyle[cs.status]?.pill"
                            >
                                <component :is="statusStyle[cs.status]?.icon" class="w-3 h-3" />
                                {{ statusStyle[cs.status]?.label ?? cs.status.replace('_', ' ') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 dark:text-gray-500 truncate">
                                @{{ cs.profile.username }}
                            </span>

                            <div class="flex items-center gap-1 shrink-0">
                                <span
                                    v-for="(data, field) in cs.changes"
                                    :key="field"
                                    class="text-[11px] w-5 h-5 rounded flex items-center justify-center font-mono font-medium border transition-colors"
                                    :class="
                                        data.status === 'approved'
                                            ? 'bg-green-100 dark:bg-green-900/20 border-green-200 dark:border-green-700/40 text-green-700 dark:text-green-400'
                                            : data.status === 'rejected'
                                              ? 'bg-red-100 dark:bg-red-900/20 border-red-200 dark:border-red-700/40 text-red-500 dark:text-red-400'
                                              : 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500'
                                    "
                                    :title="field"
                                >
                                    {{ fieldIcon[field] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <template v-if="['pending', 'in_review'].includes(cs.status)">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-20 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden"
                                >
                                    <div
                                        class="h-full rounded-full transition-all"
                                        :class="
                                            progressPercent(cs.changes) === 100
                                                ? 'bg-green-500'
                                                : 'bg-primary'
                                        "
                                        :style="{ width: progressPercent(cs.changes) + '%' }"
                                    />
                                </div>
                                <span
                                    class="text-[11px] text-gray-400 dark:text-gray-500 tabular-nums w-8 text-right"
                                >
                                    {{ progressPercent(cs.changes) }}%
                                </span>
                            </div>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                {{ pendingFieldCount(cs.changes) }} of
                                {{ totalFieldCount(cs.changes) }} pending
                            </span>
                        </template>

                        <template v-else-if="cs.status === 'applied'">
                            <span
                                class="inline-flex items-center gap-1 text-[11px] text-green-600 dark:text-green-400"
                            >
                                <CheckCircleIcon class="w-3 h-3" />
                                {{ approvedFieldCount(cs.changes) }}/{{
                                    totalFieldCount(cs.changes)
                                }}
                                applied
                            </span>
                        </template>

                        <span class="text-[11px] text-gray-400 dark:text-gray-500">
                            {{ formatDate(cs.created_at) }}
                        </span>
                    </div>

                    <svg
                        class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-gray-500 dark:group-hover:text-gray-400 transition-colors shrink-0"
                        viewBox="0 0 16 16"
                        fill="none"
                    >
                        <path
                            d="M6 4l4 4-4 4"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </a>
            </div>

            <div
                v-if="!loading && meta.last_page > 1"
                class="flex items-center justify-center gap-1 px-5 py-4 border-t border-gray-100 dark:border-gray-700/60"
            >
                <button
                    @click="load(meta.current_page - 1)"
                    :disabled="meta.current_page === 1"
                    class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center"
                >
                    <svg viewBox="0 0 16 16" fill="none" class="w-3.5 h-3.5">
                        <path
                            d="M10 4L6 8l4 4"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>

                <template v-for="page in meta.last_page" :key="page">
                    <button
                        v-if="
                            page === 1 ||
                            page === meta.last_page ||
                            Math.abs(page - meta.current_page) <= 1
                        "
                        @click="load(page)"
                        class="w-8 h-8 rounded-lg text-sm transition-colors"
                        :class="
                            page === meta.current_page
                                ? 'bg-primary text-white font-medium'
                                : 'text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                        "
                    >
                        {{ page }}
                    </button>
                    <span
                        v-else-if="Math.abs(page - meta.current_page) === 2"
                        class="text-gray-300 dark:text-gray-600 text-sm w-8 text-center"
                    >
                        &hellip;
                    </span>
                </template>

                <button
                    @click="load(meta.current_page + 1)"
                    :disabled="meta.current_page === meta.last_page"
                    class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center"
                >
                    <svg viewBox="0 0 16 16" fill="none" class="w-3.5 h-3.5">
                        <path
                            d="M6 4l4 4-4 4"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useUtils } from '@/composables/useUtils'
import {
    SquaresPlusIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    MagnifyingGlassCircleIcon
} from '@heroicons/vue/24/outline'
import { ref, onMounted, watch } from 'vue'
import axios from '~/plugins/axios'

const { formatDate } = useUtils()

const changesets = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0, per_page: 15 })
const loading = ref(false)
const activeFilter = ref('pending')
const api = axios.getAxiosInstance()

const FILTERS = [
    { key: 'pending', label: 'Pending', icon: ClockIcon, activeColor: 'text-amber-500' },
    {
        key: 'in_review',
        label: 'In Review',
        icon: MagnifyingGlassCircleIcon,
        activeColor: 'text-blue-500'
    },
    { key: 'applied', label: 'Applied', icon: CheckCircleIcon, activeColor: 'text-green-500' },
    { key: 'rejected', label: 'Rejected', icon: XCircleIcon, activeColor: 'text-red-500' }
]

const statusStyle = {
    pending: {
        label: 'Pending',
        icon: ClockIcon,
        pill: 'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400'
    },
    in_review: {
        label: 'In Review',
        icon: MagnifyingGlassCircleIcon,
        pill: 'bg-blue-100  dark:bg-blue-900/20  text-blue-700  dark:text-blue-400'
    },
    applied: {
        label: 'Applied',
        icon: CheckCircleIcon,
        pill: 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400'
    },
    rejected: {
        label: 'Rejected',
        icon: XCircleIcon,
        pill: 'bg-red-100   dark:bg-red-900/20   text-red-700   dark:text-red-400'
    }
}

const fieldIcon = {
    title: 'T',
    description: 'D',
    hashtags: '#',
    header_path: 'H',
    icon_path: 'I'
}

async function load(page = 1) {
    loading.value = true
    try {
        const res = await api.get('/api/v1/admin/starter-kits/pending-changes', {
            params: { status: activeFilter.value, page }
        })
        changesets.value = res.data.data
        meta.value = res.data.meta
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

watch(activeFilter, () => load(1))
onMounted(() => load(1))

function pendingFieldCount(changes) {
    return Object.values(changes).filter((f) => f.status === 'pending').length
}

function approvedFieldCount(changes) {
    return Object.values(changes).filter((f) => f.status === 'applied').length
}

function totalFieldCount(changes) {
    return Object.keys(changes).length
}

function progressPercent(changes) {
    const total = totalFieldCount(changes)
    if (!total) return 0
    return Math.round(((total - pendingFieldCount(changes)) / total) * 100)
}
</script>
