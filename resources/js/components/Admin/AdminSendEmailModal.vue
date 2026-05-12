<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="send-email-title"
        >
            <div class="flex min-h-screen items-center justify-center px-4 py-8 text-center">
                <div
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm dark:bg-black/70"
                    @click="handleClose"
                />

                <div
                    class="relative w-full max-w-xl rounded-3xl border border-gray-200 bg-white p-6 text-left shadow-2xl dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20"
                        >
                            <EnvelopeIcon class="h-6 w-6 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3
                                id="send-email-title"
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Send Email
                            </h3>
                            <p class="mt-1 text-sm leading-5 text-gray-600 dark:text-gray-400">
                                Send an admin email to this user.
                            </p>
                        </div>
                        <button
                            type="button"
                            :disabled="isSubmitting"
                            @click="handleClose"
                            class="shrink-0 rounded-lg p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                        >
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                    </div>

                    <div
                        class="mt-5 flex items-center gap-3 rounded-2xl border border-gray-200 bg-gray-50/60 p-3 dark:border-gray-800 dark:bg-gray-800/40"
                    >
                        <img
                            :src="profile?.avatar"
                            :alt="profile?.username"
                            class="h-10 w-10 shrink-0 rounded-full border-2 border-white bg-gray-100 object-cover dark:border-gray-900"
                            @error="onAvatarError"
                        />
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <p
                                    class="truncate text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ profile?.name || profile?.username }}
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    @{{ profile?.username }}
                                </span>
                            </div>
                            <p
                                class="mt-0.5 flex items-center gap-1 truncate text-xs text-gray-500 dark:text-gray-400"
                            >
                                <EnvelopeIcon class="h-3 w-3 shrink-0" />
                                <span class="truncate">
                                    {{ profile?.email || 'No email on file' }}
                                </span>
                            </p>
                        </div>
                        <div
                            v-if="!profile?.email_verified"
                            class="shrink-0 rounded-lg bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                        >
                            Unverified
                        </div>
                        <div
                            v-else
                            class="shrink-0 rounded-lg bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                        >
                            Verified
                        </div>
                    </div>

                    <div class="mt-5">
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Template
                        </label>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            <button
                                v-for="tpl in templates"
                                :key="tpl.id"
                                type="button"
                                @click="applyTemplate(tpl)"
                                :class="[
                                    'rounded-xl border px-3 py-2 text-left text-xs font-medium transition',
                                    activeTemplateId === tpl.id
                                        ? 'border-primary bg-primary/5 text-primary'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                ]"
                            >
                                <component :is="tpl.icon" class="mb-1 h-4 w-4" />
                                <div>{{ tpl.label }}</div>
                            </button>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label
                            for="email-subject"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Subject
                        </label>
                        <input
                            id="email-subject"
                            v-model="subject"
                            type="text"
                            maxlength="120"
                            placeholder="e.g. Important account information"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-transparent focus:ring-2 focus:ring-primary/60 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>

                    <div class="mt-4">
                        <div class="mb-2 flex items-center justify-between">
                            <label
                                for="email-message"
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Message
                            </label>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {{ message.length }} / {{ MAX_MESSAGE }}
                            </span>
                        </div>
                        <textarea
                            id="email-message"
                            v-model="message"
                            :maxlength="MAX_MESSAGE"
                            rows="7"
                            placeholder="Write your message to the user…"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-transparent focus:ring-2 focus:ring-primary/60 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                        />
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                            Plain text. The user's display name will be included automatically.
                        </p>
                    </div>

                    <div
                        class="mt-5 space-y-3 rounded-2xl border border-gray-200 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40"
                    >
                        <label class="flex cursor-pointer items-start gap-3">
                            <input
                                v-model="ccAdmin"
                                type="checkbox"
                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-primary focus:ring-primary/60 dark:border-gray-600 dark:bg-gray-700"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    BCC admin team
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    Send a copy to the admin distribution list for the record
                                </p>
                            </div>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3">
                            <input
                                v-model="logAsAudit"
                                type="checkbox"
                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-primary focus:ring-primary/60 dark:border-gray-600 dark:bg-gray-700"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    Record in audit log
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    Log this email in the profile's audit trail
                                </p>
                            </div>
                        </label>
                    </div>

                    <div
                        v-if="!canSend"
                        class="mt-4 flex gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 dark:border-amber-900/40 dark:bg-amber-900/20 dark:text-amber-300"
                    >
                        <ExclamationTriangleIcon class="h-4 w-4 shrink-0" />
                        <span v-if="!profile?.email">
                            This user has no email address on file and cannot be emailed.
                        </span>
                        <span v-else-if="!profile?.email_verified">
                            This user's email is not verified. They may not receive this message.
                        </span>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button
                            type="button"
                            :disabled="isSubmitting"
                            @click="handleClose"
                            class="flex-1 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            :disabled="!canSubmit"
                            @click="handleSubmit"
                            :class="[
                                'inline-flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium text-white transition',
                                canSubmit
                                    ? 'bg-primary hover:bg-[#d9244a]'
                                    : 'cursor-not-allowed bg-gray-300 dark:bg-gray-700'
                            ]"
                        >
                            <PaperAirplaneIcon v-if="!isSubmitting" class="h-4 w-4" />
                            <ArrowPathIcon v-else class="h-4 w-4 animate-spin" />
                            {{ isSubmitting ? 'Sending…' : 'Send Email' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, onUnmounted, ref, watch } from 'vue'
import {
    ArrowPathIcon,
    ChatBubbleLeftRightIcon,
    EnvelopeIcon,
    ExclamationCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    PaperAirplaneIcon,
    ShieldExclamationIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'

const MAX_MESSAGE = 2000
const DEFAULT_AVATAR = '/storage/avatars/default.jpg'

const props = defineProps({
    show: { type: Boolean, default: false },
    profile: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['close', 'send'])

const subject = ref('')
const message = ref('')
const ccAdmin = ref(false)
const logAsAudit = ref(true)
const isSubmitting = ref(false)
const activeTemplateId = ref(null)

const templates = [
    {
        id: 'blank',
        label: 'Blank',
        icon: ChatBubbleLeftRightIcon,
        subject: '',
        body: ''
    },
    {
        id: 'general',
        label: 'General',
        icon: InformationCircleIcon,
        subject: 'A message from the PornTk team',
        body: 'Hi {name},\n\nWe wanted to reach out about your account on PornTk.\n\n\n\nIf you have any questions, please reply to this email.\n\nThanks,\nThe PornTk team'
    },
    {
        id: 'tos',
        label: 'Policy Warning',
        icon: ExclamationCircleIcon,
        subject: 'Important: PornTk Community Guidelines',
        body: "Hi {name},\n\nWe're reaching out because some recent activity on your account appears to conflict with the PornTk Community Guidelines.\n\nPlease take a moment to review our guidelines. Continued violations may result in further action on your account.\n\nIf you believe this was sent in error, please reply to this email.\n\nThanks,\nThe PornTk team"
    },
    {
        id: 'security',
        label: 'Security',
        icon: ShieldExclamationIcon,
        subject: 'Security notice for your PornTk account',
        body: "Hi {name},\n\nWe detected activity on your account that we wanted to make you aware of.\n\n\n\nIf this wasn't you, please reset your password immediately and enable two-factor authentication.\n\nThanks,\nThe PornTk team"
    }
]

const canSend = computed(() => !!props.profile?.email && !!props.profile?.email_verified)

const canSubmit = computed(
    () =>
        !isSubmitting.value &&
        subject.value.trim().length > 0 &&
        message.value.trim().length > 0 &&
        canSend.value
)

const applyTemplate = (tpl) => {
    activeTemplateId.value = tpl.id
    const name = props.profile?.name || props.profile?.username || 'there'
    subject.value = tpl.subject
    message.value = tpl.body.replace(/\{name\}/g, name)
}

const onAvatarError = (e) => {
    if (e.target.dataset.fallbackApplied) return
    e.target.dataset.fallbackApplied = '1'
    e.target.src = DEFAULT_AVATAR
}

const handleSubmit = () => {
    if (!canSubmit.value) return
    isSubmitting.value = true
    emit('send', {
        subject: subject.value.trim(),
        message: message.value.trim(),
        ccAdmin: ccAdmin.value,
        logAsAudit: logAsAudit.value,
        templateId: activeTemplateId.value,
        done: () => {
            isSubmitting.value = false
        }
    })
}

const handleClose = () => {
    if (isSubmitting.value) return
    emit('close')
}

watch(
    () => props.show,
    (open) => {
        if (open) {
            subject.value = ''
            message.value = ''
            activeTemplateId.value = null
            ccAdmin.value = false
            logAsAudit.value = true
            isSubmitting.value = false
            document.body.style.overflow = 'hidden'
        } else {
            document.body.style.overflow = ''
        }
    },
    { immediate: true }
)

const onKeydown = (event) => {
    if (!props.show) return
    if (event.key === 'Escape') handleClose()
}
if (typeof window !== 'undefined') {
    window.addEventListener('keydown', onKeydown)
}

onUnmounted(() => {
    document.body.style.overflow = ''
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown)
    }
})
</script>
