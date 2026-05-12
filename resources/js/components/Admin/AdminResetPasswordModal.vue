<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="reset-password-title"
        >
            <div class="flex min-h-screen items-center justify-center px-4 py-8 text-center">
                <div
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm dark:bg-black/70"
                    @click="handleClose"
                />

                <div
                    class="relative w-full max-w-lg rounded-3xl border border-gray-200 bg-white p-6 text-left shadow-2xl dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20"
                        >
                            <KeyIcon class="h-6 w-6 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3
                                id="reset-password-title"
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Reset Password
                            </h3>
                            <p class="mt-1 text-sm leading-5 text-gray-600 dark:text-gray-400">
                                Set a new password for
                                <strong class="text-gray-900 dark:text-white"
                                    >@{{ profile?.username }}</strong
                                >.
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

                    <div class="mt-6">
                        <div class="mb-2 flex items-center justify-between">
                            <label
                                for="reset-password-input"
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                New Password
                            </label>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {{ password.length }} chars
                            </span>
                        </div>

                        <div class="relative">
                            <input
                                id="reset-password-input"
                                ref="passwordInputRef"
                                v-model="password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                spellcheck="false"
                                class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 pr-[104px] font-mono text-sm tracking-tight text-gray-900 outline-none transition focus:border-transparent focus:ring-2 focus:ring-primary/60 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            />

                            <div class="absolute inset-y-0 right-0 flex items-center gap-0.5 pr-2">
                                <button
                                    type="button"
                                    :title="showPassword ? 'Hide password' : 'Show password'"
                                    @click="showPassword = !showPassword"
                                    class="rounded-lg p-1.5 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                >
                                    <EyeIcon v-if="!showPassword" class="h-4 w-4" />
                                    <EyeSlashIcon v-else class="h-4 w-4" />
                                </button>

                                <button
                                    type="button"
                                    title="Generate new password"
                                    @click="regeneratePassword"
                                    class="rounded-lg p-1.5 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                >
                                    <ArrowPathIcon class="h-4 w-4" />
                                </button>

                                <button
                                    type="button"
                                    :title="copied ? 'Copied!' : 'Copy to clipboard'"
                                    @click="copyPassword"
                                    class="rounded-lg p-1.5 transition"
                                    :class="
                                        copied
                                            ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
                                            : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200'
                                    "
                                >
                                    <CheckIcon v-if="copied" class="h-4 w-4" />
                                    <ClipboardDocumentIcon v-else class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="flex gap-1">
                                <div
                                    v-for="i in 4"
                                    :key="i"
                                    class="h-1.5 flex-1 rounded-full transition-colors"
                                    :class="strengthBarClass(i)"
                                />
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-xs font-medium" :class="strengthTextClass">
                                    {{ strengthLabel }}
                                </p>
                                <p
                                    v-if="copied"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400"
                                >
                                    <CheckIcon class="h-3 w-3" />
                                    Copied to clipboard
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Length:</span>
                        <button
                            v-for="len in [20, 24, 28, 32]"
                            :key="len"
                            type="button"
                            @click="setLength(len)"
                            class="rounded-lg border px-2.5 py-1 font-medium transition"
                            :class="
                                preferredLength === len
                                    ? 'border-primary bg-primary/10 text-primary'
                                    : 'border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                            "
                        >
                            {{ len }}
                        </button>
                    </div>

                    <div
                        class="mt-5 space-y-3 rounded-2xl border border-gray-200 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40"
                    >
                        <label
                            v-for="opt in options"
                            :key="opt.key"
                            class="flex cursor-pointer items-start gap-3"
                            :class="{ 'opacity-60 cursor-not-allowed': opt.disabled }"
                        >
                            <input
                                type="checkbox"
                                :checked="opt.model.value"
                                :disabled="opt.disabled"
                                @change="opt.model.value = $event.target.checked"
                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-primary focus:ring-primary/60 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-700"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ opt.label }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    {{ opt.description }}
                                </p>
                            </div>
                        </label>
                    </div>

                    <div
                        v-if="!canEmail && sendEmail"
                        class="mt-4 flex gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 dark:border-amber-900/40 dark:bg-amber-900/20 dark:text-amber-300"
                    >
                        <ExclamationTriangleIcon class="h-4 w-4 shrink-0" />
                        <span>
                            This user has no verified email address. Copy the password manually and
                            share it through another secure channel.
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
                            <KeyIcon v-if="!isSubmitting" class="h-4 w-4" />
                            <ArrowPathIcon v-else class="h-4 w-4 animate-spin" />
                            {{ isSubmitting ? 'Resetting…' : 'Reset Password' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, nextTick, onUnmounted, ref, watch } from 'vue'
import {
    ArrowPathIcon,
    CheckIcon,
    ClipboardDocumentIcon,
    ExclamationTriangleIcon,
    EyeIcon,
    EyeSlashIcon,
    KeyIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    show: { type: Boolean, default: false },
    profile: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['close', 'reset'])

const MIN_LENGTH = 12

const CHARSETS = {
    upper: 'ABCDEFGHJKLMNPQRSTUVWXYZ',
    lower: 'abcdefghijkmnpqrstuvwxyz',
    digits: '23456789',
    symbols: '!@#$%^&*()_+-=[]{}|;:,.<>?~'
}

const password = ref('')
const preferredLength = ref(24)
const showPassword = ref(true)
const copied = ref(false)
const isSubmitting = ref(false)
const passwordInputRef = ref(null)

const sendEmail = ref(true)
const forceChange = ref(true)
const revokeSessions = ref(true)

let copyTimer = null

const canEmail = computed(() => !!props.profile?.email_verified && !!props.profile?.email)

const options = computed(() => [
    {
        key: 'sendEmail',
        label: 'Email password to user',
        description: canEmail.value
            ? `Send this password to ${props.profile?.email}`
            : 'User has no verified email — delivery will be skipped',
        model: sendEmail,
        disabled: false
    },
    {
        key: 'forceChange',
        label: 'Require password change on next login',
        description: 'User will be prompted to set their own password when they sign in',
        model: forceChange,
        disabled: false
    },
    {
        key: 'revokeSessions',
        label: 'Revoke all active sessions',
        description: 'Log the user out of every device and invalidate API tokens',
        model: revokeSessions,
        disabled: false
    }
])

const secureRandomInt = (max) => {
    const arr = new Uint32Array(1)
    window.crypto.getRandomValues(arr)
    return arr[0] % max
}

const pickFrom = (chars) => chars[secureRandomInt(chars.length)]

const generatePassword = (length = 24) => {
    const all = CHARSETS.upper + CHARSETS.lower + CHARSETS.digits + CHARSETS.symbols
    const out = [
        pickFrom(CHARSETS.upper),
        pickFrom(CHARSETS.lower),
        pickFrom(CHARSETS.digits),
        pickFrom(CHARSETS.symbols)
    ]
    for (let i = out.length; i < length; i++) {
        out.push(pickFrom(all))
    }
    for (let i = out.length - 1; i > 0; i--) {
        const j = secureRandomInt(i + 1)
        ;[out[i], out[j]] = [out[j], out[i]]
    }
    return out.join('')
}

const regeneratePassword = () => {
    password.value = generatePassword(preferredLength.value)
    copied.value = false
}

const setLength = (len) => {
    preferredLength.value = len
    regeneratePassword()
}

const strengthScore = computed(() => {
    const p = password.value
    if (!p) return 0
    let score = 0
    if (p.length >= 12) score++
    if (p.length >= 20) score++
    const variety = /[a-z]/.test(p) + /[A-Z]/.test(p) + /\d/.test(p) + /[^A-Za-z0-9]/.test(p)
    if (variety >= 3) score++
    if (variety === 4 && p.length >= 16) score++
    return Math.min(score, 4)
})

const strengthLabel = computed(() => {
    if (password.value.length < MIN_LENGTH) {
        return `Too short — minimum ${MIN_LENGTH} characters`
    }
    return ['Very weak', 'Weak', 'Fair', 'Strong', 'Very strong'][strengthScore.value]
})

const strengthTextClass = computed(() => {
    if (password.value.length < MIN_LENGTH) return 'text-red-600 dark:text-red-400'
    return [
        'text-red-600 dark:text-red-400',
        'text-orange-600 dark:text-orange-400',
        'text-yellow-600 dark:text-yellow-400',
        'text-emerald-600 dark:text-emerald-400',
        'text-emerald-600 dark:text-emerald-400'
    ][strengthScore.value]
})

const strengthBarClass = (index) => {
    const active = index <= strengthScore.value
    if (!active) return 'bg-gray-200 dark:bg-gray-700'
    const score = strengthScore.value
    if (score <= 1) return 'bg-red-500'
    if (score === 2) return 'bg-yellow-500'
    return 'bg-emerald-500'
}

const copyPassword = async () => {
    if (!password.value) return
    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(password.value)
        } else {
            const el = passwordInputRef.value
            if (el) {
                const prevType = el.type
                el.type = 'text'
                el.select()
                document.execCommand('copy')
                el.type = prevType
                el.blur()
            }
        }
        copied.value = true
        if (copyTimer) clearTimeout(copyTimer)
        copyTimer = setTimeout(() => (copied.value = false), 2000)
    } catch (err) {
        console.error('Clipboard copy failed:', err)
    }
}

const canSubmit = computed(() => !isSubmitting.value && password.value.length >= MIN_LENGTH)

const handleSubmit = () => {
    if (!canSubmit.value) return
    isSubmitting.value = true
    emit('reset', {
        password: password.value,
        sendEmail: sendEmail.value && canEmail.value,
        forceChange: forceChange.value,
        revokeSessions: revokeSessions.value,
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
    async (open) => {
        if (open) {
            regeneratePassword()
            copied.value = false
            isSubmitting.value = false
            showPassword.value = true
            document.body.style.overflow = 'hidden'
            await nextTick()
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
    if (copyTimer) clearTimeout(copyTimer)
    document.body.style.overflow = ''
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown)
    }
})
</script>
