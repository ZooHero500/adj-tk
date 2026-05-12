<template>
    <SettingsLayout>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <button
                    class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 cursor-pointer transition-colors"
                    @click="$router.back()"
                >
                    <i class="bx bx-arrow-back text-[20px] mr-1"></i>
                </button>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                    {{ $t('settings.security') }}
                </h1>
            </div>

            <hr class="border-gray-200 dark:border-gray-800" />

            <section class="my-8">
                <h2 class="tracking-tight font-light mb-4 text-gray-700 dark:text-gray-300">
                    {{ $t('settings.passwordAndAuthentication') }}
                </h2>

                <div class="flex flex-col gap-3 mb-6">
                    <div
                        class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-sm"
                    >
                        <div class="flex justify-between items-center p-4">
                            <div class="flex items-center gap-5">
                                <h3 class="font-medium mb-0 text-gray-900 dark:text-gray-200">
                                    {{ $t('settings.password') }}
                                </h3>
                            </div>
                            <div class="flex items-center">
                                <button
                                    class="text-sm text-blue-600 dark:text-blue-400 font-medium cursor-pointer hover:text-blue-500 dark:hover:text-blue-300 transition-colors"
                                    @click="openChangePasswordModal"
                                >
                                    {{ $t('settings.change') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-sm"
                    >
                        <div class="px-4 py-6 flex items-center justify-between">
                            <div class="flex flex-col max-w-[60%]">
                                <h3 class="font-medium mb-2 text-gray-900 dark:text-gray-200">
                                    {{ $t('settings.twoFactorAuthentication') }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-light">
                                    {{ $t('settings.addAnExtraLayerOfSecurity') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-8">
                                <span
                                    class="text-sm"
                                    :class="
                                        twoFactorEnabled
                                            ? 'bg-green-100 dark:bg-green-500/10 p-2 rounded-lg text-green-700 dark:text-green-400'
                                            : 'text-gray-500 dark:text-gray-400'
                                    "
                                >
                                    {{
                                        twoFactorEnabled
                                            ? $t('common.enabled')
                                            : $t('common.disabled')
                                    }}
                                </span>

                                <button
                                    v-if="twoFactorEnabled"
                                    class="text-sm font-medium cursor-pointer text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors"
                                    @click="disableTwoFactor"
                                >
                                    {{ $t('common.disable') }}
                                </button>

                                <button
                                    v-else
                                    class="text-sm font-medium cursor-pointer text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors"
                                    @click="enableTwoFactor"
                                >
                                    {{ $t('common.enable') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="isSettingUp2FA"
                        class="bg-white dark:bg-neutral-950 border border-gray-200 dark:border-neutral-800 rounded-xl shadow-sm"
                    >
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                {{ $t('settings.setup2FA') }}
                            </h3>

                            <div class="space-y-6">
                                <div class="text-center">
                                    <h4 class="font-medium mb-2 text-gray-900 dark:text-gray-200">
                                        {{ $t('settings.setup2FAStep1') }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        {{ $t('settings.setup2FAStep1Message') }}
                                    </p>

                                    <div class="flex justify-center mb-4">
                                        <div
                                            class="bg-white dark:bg-neutral-900 p-4 rounded-lg border border-gray-200 dark:border-neutral-700 inline-block"
                                        >
                                            <div v-html="qrCode" class="w-48 h-48"></div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-medium mb-2 text-gray-900 dark:text-gray-200">
                                        {{ $t('settings.setup2FAStep2') }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        {{ $t('settings.setup2FAStep2Message') }}
                                    </p>

                                    <div class="max-w-xs mx-auto">
                                        <div class="relative">
                                            <input
                                                type="text"
                                                v-model="twoFactorVerifyCode"
                                                maxlength="6"
                                                placeholder="000000"
                                                class="w-full px-4 py-3 text-center text-lg font-mono tracking-widest border border-gray-300 dark:border-neutral-700 rounded-lg bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :class="{
                                                    'border-red-500 dark:border-red-500 focus:ring-red-500 focus:border-red-500':
                                                        has2FAError,
                                                    'pr-10': isVerifying
                                                }"
                                                @input="handle2FAInput"
                                                @keyup.enter="verify2FA"
                                                :disabled="isVerifying"
                                            />
                                            <div
                                                v-if="isVerifying"
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2"
                                            >
                                                <div
                                                    class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"
                                                ></div>
                                            </div>
                                        </div>

                                        <div
                                            v-if="has2FAError"
                                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {{ twoFactorError }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-3 justify-center pt-4">
                                    <button
                                        class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-neutral-800 border border-transparent dark:border-neutral-700 rounded-lg hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors cursor-pointer"
                                        @click="cancel2FASetup"
                                        :disabled="isVerifying"
                                    >
                                        {{ $t('common.cancel') }}
                                    </button>

                                    <AnimatedButton
                                        @click="verify2FA"
                                        :disabled="
                                            !twoFactorVerifyCode ||
                                            twoFactorVerifyCode.length !== 6 ||
                                            isVerifying
                                        "
                                    >
                                        <div
                                            v-if="isVerifying"
                                            class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                                        ></div>
                                        {{
                                            isVerifying
                                                ? $t('settings.verifyingDotDotDot')
                                                : $t('settings.verifyAndEnable')
                                        }}
                                    </AnimatedButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div
            v-if="showChangePassword"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
            @click.self="cancelChangePassword"
        >
            <div
                class="w-full max-w-md rounded-2xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 shadow-2xl"
            >
                <div class="p-6">
                    <div class="mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $t('settings.changePassword') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $t('settings.passwordAndAuthentication') }}
                        </p>
                    </div>

                    <div
                        v-if="hasPasswordChangeError"
                        class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-500/20 dark:bg-red-500/10"
                    >
                        <div class="font-semibold text-red-700 dark:text-red-400 text-sm mb-1">
                            {{ $t('settings.oopsTheFollowingErrorsOccured') }}
                        </div>
                        <div
                            v-for="(error, index) in passwordErrors"
                            :key="index"
                            class="text-sm text-red-600 dark:text-red-300"
                        >
                            {{ error[0] }}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                {{ $t('settings.currentPassword') }}
                            </label>
                            <input
                                type="password"
                                v-model="currentPassword"
                                class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                {{ $t('settings.newPassword') }}
                            </label>
                            <input
                                type="password"
                                v-model="newPassword"
                                class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                {{ $t('settings.confirmNewPassword') }}
                            </label>
                            <input
                                type="password"
                                v-model="confirmPassword"
                                class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-3 py-2 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-neutral-800 border border-transparent dark:border-neutral-700 rounded-lg hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors cursor-pointer"
                            @click="cancelChangePassword"
                        >
                            {{ $t('common.cancel') }}
                        </button>

                        <button
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                            @click="changePassword"
                        >
                            {{ $t('settings.changePassword') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </SettingsLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SettingsLayout from '~/layouts/SettingsLayout.vue'
import axios from '~/plugins/axios'
import { useAlertModal } from '@/composables/useAlertModal.js'
import { useI18n } from 'vue-i18n'
import AnimatedButton from '@/components/AnimatedButton.vue'

const twoFactorEnabled = ref(false)
const axiosInstance = axios.getAxiosInstance()
const { alertModal, confirmModal } = useAlertModal()
const showChangePassword = ref(false)
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const hasPasswordChangeError = ref(false)
const passwordErrors = ref([])
const qrCode = ref()
const isSettingUp2FA = ref(false)
const twoFactorVerifyCode = ref('')
const isVerifying = ref(false)
const has2FAError = ref(false)
const twoFactorError = ref('')
const { t } = useI18n()

const resetPasswordForm = () => {
    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
    hasPasswordChangeError.value = false
    passwordErrors.value = []
}

const openChangePasswordModal = () => {
    resetPasswordForm()
    showChangePassword.value = true
}

const loadSecurityConfig = () => {
    axiosInstance.get('/api/v1/account/settings/security-config').then((res) => {
        twoFactorEnabled.value = res.data.data.two_factor_enabled
    })
}

const disableTwoFactor = async () => {
    const result = await confirmModal(
        t('settings.disableTwoFactor'),
        t('settings.disableTwoFactorMessage'),
        t('settings.disable2FAButton'),
        t('common.cancel')
    )

    if (result) {
        await axiosInstance
            .post('/api/v1/account/settings/disable-2fa')
            .then(() => {
                twoFactorEnabled.value = false
            })
            .finally(() => {
                alertModal(
                    t('settings.twoFactorAuthDisabled'),
                    t('settings.twoFactorAuthDisabledMessage')
                )
            })
    }
}

const enableTwoFactor = async () => {
    const result = await confirmModal(
        t('settings.enableTwoFactor'),
        t('settings.enableTwoFactorMessage'),
        t('common.enable'),
        t('common.cancel')
    )

    if (result) {
        await axiosInstance.post('/api/v1/account/settings/setup-2fa').then((res) => {
            qrCode.value = res.data.data.qr
            isSettingUp2FA.value = true
            twoFactorVerifyCode.value = ''
            has2FAError.value = false
            twoFactorError.value = ''
        })
    }
}

const handle2FAInput = (event) => {
    const value = event.target.value.replace(/[^\d]/g, '')
    twoFactorVerifyCode.value = value

    if (has2FAError.value) {
        has2FAError.value = false
        twoFactorError.value = ''
    }
}

const verify2FA = async () => {
    if (!twoFactorVerifyCode.value || twoFactorVerifyCode.value.length !== 6) {
        has2FAError.value = true
        twoFactorError.value = t('settings.pleaseEnterA6DigitCode')
        return
    }

    isVerifying.value = true
    has2FAError.value = false
    twoFactorError.value = ''

    try {
        await axiosInstance.post('/api/v1/account/settings/confirm-2fa', {
            code: twoFactorVerifyCode.value
        })

        twoFactorEnabled.value = true
        isSettingUp2FA.value = false

        await alertModal(t('settings.twoFactorEnabled'), t('settings.twoFactorEnabledMessage'))
    } catch (error) {
        has2FAError.value = true
        if (error.response?.data?.message) {
            twoFactorError.value = error.response.data.message
        } else if (error.response?.data?.errors?.code) {
            twoFactorError.value = error.response.data.errors.code[0]
        } else {
            twoFactorError.value = t('settings.invalidVerificationCodePleaseTryAgain')
        }
    } finally {
        isVerifying.value = false
    }
}

const cancel2FASetup = () => {
    isSettingUp2FA.value = false
    twoFactorVerifyCode.value = ''
    has2FAError.value = false
    twoFactorError.value = ''
    qrCode.value = null
}

const changePassword = async () => {
    hasPasswordChangeError.value = false
    passwordErrors.value = []

    try {
        await axiosInstance.post('/api/v1/account/settings/update-password', {
            current_password: currentPassword.value,
            password: newPassword.value,
            password_confirmation: confirmPassword.value
        })

        showChangePassword.value = false
        resetPasswordForm()

        await alertModal(
            t('settings.successExclamation'),
            t('settings.successPasswordChangedMessage')
        )
    } catch (err) {
        hasPasswordChangeError.value = true
        passwordErrors.value = err.response?.data?.errors ?? []
    }
}

const cancelChangePassword = () => {
    showChangePassword.value = false
    resetPasswordForm()
}

onMounted(() => {
    loadSecurityConfig()
})
</script>
