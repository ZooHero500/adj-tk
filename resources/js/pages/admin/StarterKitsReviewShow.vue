<template>
    <div class="min-h-screen">
        <div v-if="loading" class="w-full h-64 flex justify-center items-center">
            <Spinner />
        </div>

        <div v-else-if="error" class="flex flex-col items-center justify-center py-24 text-center">
            <div
                class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40 flex items-center justify-center mb-4"
            >
                <ExclamationCircleIcon class="w-6 h-6 text-red-500 dark:text-red-400" />
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                Failed to load changeset
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                This changeset could not be loaded. It may have been removed or you may not have
                permission to review it.
            </p>
            <button
                @click="fetchReport(route.params.id)"
                class="mt-6 text-xs px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
            >
                Try again
            </button>
        </div>

        <div v-else>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Starter Kit Review</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Manage Starter Kit updates</p>
            </div>

            <div
                class="flex items-center justify-between gap-4 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/60 rounded-xl px-5 py-4 mb-4"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shrink-0 flex items-center justify-center"
                    >
                        <img
                            v-if="changeset.starter_kit.icon_url"
                            :src="changeset.starter_kit.icon_url"
                            class="w-full h-full object-cover"
                        />
                        <span
                            v-else
                            class="text-sm font-semibold text-gray-400 dark:text-gray-600 font-['Syne',sans-serif]"
                        >
                            {{ changeset.starter_kit.title?.charAt(0)?.toUpperCase() }}
                        </span>
                    </div>
                    <div>
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white leading-tight"
                        >
                            {{ changeset.starter_kit.title }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-0.5">
                            Submitted by
                            <span class="text-gray-700 dark:text-gray-300"
                                >@{{ changeset.profile.username }}</span
                            >
                            &middot;
                            {{ formatDate(changeset.created_at) }}
                        </p>
                    </div>
                </div>

                <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full shrink-0"
                    :class="statusStyle[changeset.status]?.pill"
                >
                    <component :is="statusStyle[changeset.status]?.icon" class="w-3.5 h-3.5" />
                    {{ statusStyle[changeset.status]?.label ?? changeset.status.replace('_', ' ') }}
                </span>
            </div>

            <div
                class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/60 rounded-xl px-5 py-4 mb-4"
            >
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-500 mb-2">
                    <span class="font-medium">Review progress</span>
                    <span class="tabular-nums">{{ reviewedCount }} / {{ totalCount }} fields</span>
                </div>
                <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-500"
                        :class="allReviewed ? 'bg-green-500' : 'bg-primary'"
                        :style="{ width: progressPercent + '%' }"
                    />
                </div>
            </div>

            <div
                v-if="allReviewed"
                class="mb-4 flex items-center gap-3 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-700/40 rounded-xl px-4 py-3"
            >
                <CheckCircleIcon class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0" />
                <p class="text-sm text-green-700 dark:text-green-400">
                    All fields reviewed — approved changes will be applied automatically.
                </p>
            </div>

            <div class="space-y-3">
                <div
                    v-for="field in fields"
                    :key="field"
                    class="bg-white dark:bg-gray-800/50 border rounded-xl overflow-hidden transition-colors duration-200"
                    :class="
                        localChanges[field].status === 'approved'
                            ? 'border-green-200 dark:border-green-700/40'
                            : localChanges[field].status === 'rejected'
                              ? 'border-red-200 dark:border-red-700/40'
                              : 'border-gray-200 dark:border-gray-700/60'
                    "
                >
                    <div
                        class="flex items-center justify-between gap-4 px-5 py-3 border-b"
                        :class="
                            localChanges[field].status === 'approved'
                                ? 'border-green-100 dark:border-green-700/30 bg-green-50 dark:bg-green-900/10'
                                : localChanges[field].status === 'rejected'
                                  ? 'border-red-100 dark:border-red-700/30 bg-red-50 dark:bg-red-900/10'
                                  : 'border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/80'
                        "
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="text-sm font-semibold text-gray-900 dark:text-white font-['Syne',sans-serif]"
                            >
                                {{ FIELD_LABELS[field] }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium rounded-full"
                                :class="
                                    statusStyle[localChanges[field].status]?.pill ??
                                    statusStyle.pending.pill
                                "
                            >
                                <component
                                    :is="
                                        statusStyle[localChanges[field].status]?.icon ??
                                        statusStyle.pending.icon
                                    "
                                    class="w-3 h-3"
                                />
                                {{ localChanges[field].status }}
                            </span>
                        </div>

                        <div
                            v-if="localChanges[field].status === 'pending'"
                            class="flex items-center gap-2 shrink-0"
                        >
                            <button
                                @click="rejectField(field)"
                                :disabled="fieldLoading[field]"
                                class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg border border-red-200 dark:border-red-700/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-40"
                            >
                                <XCircleIcon class="w-3.5 h-3.5" />
                                Reject
                            </button>
                            <button
                                @click="approveField(field)"
                                :disabled="fieldLoading[field]"
                                class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg bg-primary hover:bg-[#d42249] text-white font-medium transition-colors disabled:opacity-40"
                            >
                                <svg
                                    v-if="fieldLoading[field]"
                                    class="w-3 h-3 animate-spin"
                                    viewBox="0 0 12 12"
                                    fill="none"
                                >
                                    <circle
                                        cx="6"
                                        cy="6"
                                        r="4.5"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-dasharray="14 6"
                                    />
                                </svg>
                                <CheckCircleIcon v-else class="w-3.5 h-3.5" />
                                Approve
                            </button>
                        </div>

                        <div v-else class="shrink-0">
                            <CheckCircleIcon
                                v-if="localChanges[field].status === 'approved'"
                                class="w-4 h-4 text-green-600 dark:text-green-400"
                            />
                            <XCircleIcon v-else class="w-4 h-4 text-red-500 dark:text-red-400" />
                        </div>
                    </div>

                    <div class="px-5 py-4 space-y-3">
                        <template v-if="isMedia(field)">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p
                                        class="text-[11px] font-medium text-gray-400 dark:text-gray-600 mb-2 uppercase tracking-wider"
                                    >
                                        Current
                                    </p>
                                    <div
                                        class="aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="changeset.original[field]?.preview_url"
                                            :src="changeset.original[field]?.preview_url"
                                            class="w-full h-full"
                                            :class="
                                                field === 'icon'
                                                    ? 'object-contain p-2'
                                                    : 'object-cover'
                                            "
                                        />
                                        <span
                                            v-else
                                            class="text-xs text-gray-400 dark:text-gray-600"
                                            >No image</span
                                        >
                                    </div>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] font-medium text-gray-400 dark:text-gray-600 mb-2 uppercase tracking-wider"
                                    >
                                        Proposed
                                    </p>
                                    <div
                                        class="aspect-video rounded-lg overflow-hidden border flex items-center justify-center"
                                        :class="
                                            localChanges[field].status === 'approved'
                                                ? 'border-green-200 dark:border-green-700/40 bg-green-50 dark:bg-green-900/10'
                                                : localChanges[field].status === 'rejected'
                                                  ? 'border-red-200 dark:border-red-700/40 bg-red-50 dark:bg-red-900/10'
                                                  : 'border-primary/30 bg-primary/5'
                                        "
                                    >
                                        <template v-if="localChanges[field].action === 'delete'">
                                            <div class="flex flex-col items-center gap-1.5">
                                                <XCircleIcon class="w-5 h-5 text-red-400" />
                                                <span class="text-xs text-red-500 dark:text-red-400"
                                                    >Remove image</span
                                                >
                                            </div>
                                        </template>
                                        <img
                                            v-else-if="localChanges[field].preview_url"
                                            :src="localChanges[field].preview_url"
                                            class="w-full h-full"
                                            :class="
                                                field === 'icon'
                                                    ? 'object-contain p-2'
                                                    : 'object-cover'
                                            "
                                        />
                                        <span
                                            v-else
                                            class="text-xs text-gray-400 dark:text-gray-600"
                                            >No image</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-else-if="field === 'hashtags'">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p
                                        class="text-[11px] font-medium text-gray-400 dark:text-gray-600 mb-2 uppercase tracking-wider"
                                    >
                                        Current
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="tag in changeset.original.hashtags || []"
                                            :key="tag"
                                            class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400"
                                        >
                                            #{{ tag }}
                                        </span>
                                        <span
                                            v-if="!changeset.original.hashtags?.length"
                                            class="text-xs text-gray-400 dark:text-gray-600 italic"
                                            >none</span
                                        >
                                    </div>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] font-medium text-gray-400 dark:text-gray-600 mb-2 uppercase tracking-wider"
                                    >
                                        Proposed
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="tag in localChanges.hashtags.value || []"
                                            :key="tag"
                                            class="text-xs px-2 py-0.5 rounded-full border font-medium"
                                            :class="
                                                localChanges[field].status === 'approved'
                                                    ? 'bg-green-100 dark:bg-green-900/20 border-green-200 dark:border-green-700/40 text-green-700 dark:text-green-400'
                                                    : localChanges[field].status === 'rejected'
                                                      ? 'bg-red-100 dark:bg-red-900/20 border-red-200 dark:border-red-700/40 text-red-500 dark:text-red-400'
                                                      : 'bg-primary/10 border-primary/30 text-primary'
                                            "
                                        >
                                            #{{ tag }}
                                        </span>
                                        <span
                                            v-if="!localChanges.hashtags.value?.length"
                                            class="text-xs text-gray-400 dark:text-gray-600 italic"
                                            >none</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p
                                        class="text-[11px] font-medium text-gray-400 dark:text-gray-600 mb-2 uppercase tracking-wider"
                                    >
                                        Current
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2.5 border border-gray-100 dark:border-gray-700 min-h-[2.5rem]"
                                        :class="field === 'description' ? 'line-clamp-4' : ''"
                                    >
                                        {{ changeset.original[field] || '—' }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] font-medium text-gray-400 dark:text-gray-600 mb-2 uppercase tracking-wider"
                                    >
                                        Proposed
                                    </p>
                                    <p
                                        class="text-sm leading-relaxed rounded-lg px-3 py-2.5 border min-h-[2.5rem]"
                                        :class="[
                                            field === 'description' ? 'line-clamp-4' : '',
                                            localChanges[field].status === 'approved'
                                                ? 'text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-700/40'
                                                : localChanges[field].status === 'rejected'
                                                  ? 'text-red-400/60 line-through bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-700/40'
                                                  : 'text-gray-700 dark:text-gray-300 bg-primary/5 border-primary/20'
                                        ]"
                                    >
                                        {{ localChanges[field].value || '—' }}
                                    </p>
                                </div>
                            </div>
                        </template>

                        <div
                            v-if="
                                localChanges[field].status === 'rejected' &&
                                localChanges[field].rejection_reason
                            "
                            class="flex items-start gap-2 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-700/40 rounded-lg px-3 py-2.5"
                        >
                            <ExclamationTriangleIcon
                                class="w-3.5 h-3.5 text-red-500 dark:text-red-400 mt-0.5 shrink-0"
                            />
                            <p class="text-xs text-red-600 dark:text-red-400">
                                {{ localChanges[field].rejection_reason }}
                            </p>
                        </div>

                        <div v-if="rejectingField === field" class="space-y-2 pt-1">
                            <textarea
                                v-model="rejectionInputs[field]"
                                placeholder="Reason for rejection (optional)"
                                rows="2"
                                class="w-full text-sm bg-gray-50 dark:bg-gray-800 border border-red-200 dark:border-red-700/50 rounded-lg px-3 py-2 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-600 resize-none focus:outline-none focus:border-red-400 dark:focus:border-red-500/60 transition-colors"
                            />
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="rejectingField = null"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    @click="rejectField(field)"
                                    :disabled="fieldLoading[field]"
                                    class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/30 transition-colors disabled:opacity-40 font-medium"
                                >
                                    <svg
                                        v-if="fieldLoading[field]"
                                        class="w-3 h-3 animate-spin"
                                        viewBox="0 0 12 12"
                                        fill="none"
                                    >
                                        <circle
                                            cx="6"
                                            cy="6"
                                            r="4.5"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-dasharray="14 6"
                                        />
                                    </svg>
                                    <XCircleIcon v-else class="w-3.5 h-3.5" />
                                    Confirm rejection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="mt-6 flex items-center justify-between text-xs text-gray-400 dark:text-gray-600"
            >
                <span>Changeset #{{ changeset.id }}</span>
                <span>Starter Kit #{{ changeset.starter_kit.id }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import Spinner from '@/components/Spinner.vue'
import { useAdminStore } from '@/stores/admin'
import {
    CheckCircleIcon,
    XCircleIcon,
    ExclamationCircleIcon,
    ExclamationTriangleIcon,
    ClockIcon,
    MagnifyingGlassCircleIcon
} from '@heroicons/vue/24/outline'
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from '~/plugins/axios'

const adminStore = useAdminStore()
const api = axios.getAxiosInstance()

const changeset = ref()
const loading = ref(true)
const error = ref(false)
const fieldLoading = ref({})

const route = useRoute()
const emit = defineEmits(['updated', 'applied'])

const FIELD_LABELS = {
    title: 'Title',
    description: 'Description',
    hashtags: 'Hashtags',
    icon: 'Icon image',
    header_image: 'Header image'
}

const MEDIA_FIELDS = ['icon_path', 'header_path']

const localChanges = computed(() => changeset.value?.changes)

const rejectionInputs = ref({})
const rejectingField = ref(null)

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
    approved: {
        label: 'Approved',
        icon: CheckCircleIcon,
        pill: 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400'
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

watch(
    () => route.params.id,
    (newId) => {
        if (newId) fetchReport(newId)
    }
)

onMounted(() => {
    if (route.params.id) fetchReport(route.params.id)
})

const fetchReport = async (id) => {
    loading.value = true
    error.value = false
    try {
        const res = await api.get(`/api/v1/admin/starter-kits/pending-changes/${id}`)
        changeset.value = res.data.data
        rejectionInputs.value = Object.fromEntries(
            Object.keys(changeset.value.changes).map((f) => [f, ''])
        )
        fieldLoading.value = Object.fromEntries(
            Object.keys(changeset.value.changes).map((f) => [f, false])
        )
    } catch (err) {
        error.value = true
        console.error(err)
    } finally {
        loading.value = false
    }
}

const fields = computed(() => (localChanges.value ? Object.keys(localChanges.value) : []))

const reviewedCount = computed(
    () => fields.value.filter((f) => localChanges.value[f].status !== 'pending').length
)
const totalCount = computed(() => fields.value.length)
const allReviewed = computed(() => reviewedCount.value === totalCount.value)
const progressPercent = computed(() =>
    totalCount.value ? Math.round((reviewedCount.value / totalCount.value) * 100) : 0
)

function isMedia(field) {
    return MEDIA_FIELDS.includes(field)
}

async function approveField(field) {
    fieldLoading.value[field] = true
    rejectingField.value = null
    try {
        await api
            .post(
                `/api/v1/admin/starter-kits/pending-changes/${changeset.value.id}/fields/${field}/approve`
            )
            .finally(async () => {
                await adminStore.fetchReportsCount()
            })
        localChanges.value[field].status = 'approved'
        localChanges.value[field].rejection_reason = null
        emit('updated', { field, status: 'approved' })
        if (allReviewed.value) emit('applied')
    } catch (e) {
        console.error(e)
    } finally {
        fieldLoading.value[field] = false
    }
}

async function rejectField(field) {
    const reason = rejectionInputs.value[field]?.trim() || null
    fieldLoading.value[field] = true
    try {
        await api
            .post(
                `/api/v1/admin/starter-kits/pending-changes/${changeset.value.id}/fields/${field}/reject`,
                { reason }
            )
            .finally(async () => {
                await adminStore.fetchReportsCount()
            })
        localChanges.value[field].status = 'rejected'
        localChanges.value[field].rejection_reason = reason
        rejectingField.value = null
        emit('updated', { field, status: 'rejected', reason })
        if (allReviewed.value) emit('applied')
    } catch (e) {
        console.error(e)
    } finally {
        fieldLoading.value[field] = false
    }
}

function toggleReject(field) {
    rejectingField.value = rejectingField.value === field ? null : field
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleString('en-CA', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>
