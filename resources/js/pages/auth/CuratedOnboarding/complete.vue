<template>
    <BlankLayout>
        <div class="onboarding relative min-h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-1/2 -left-1/4 h-[900px] w-[900px] rounded-full bg-gradient-to-br from-[#F02C56]/40 via-[#F02C56]/20 to-transparent blur-3xl animate-drift-slow"
                />
                <div
                    class="absolute -bottom-1/3 -right-1/4 h-[700px] w-[700px] rounded-full bg-gradient-to-tl from-[#F02C56]/30 via-rose-400/20 to-transparent blur-3xl animate-drift-slow-reverse"
                />
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-rose-400/20 via-[#F02C56]/10 to-transparent blur-2xl animate-drift-slow"
                />
            </div>

            <div
                class="relative z-10 flex min-h-screen items-center justify-center px-4 py-12 sm:py-16"
            >
                <Transition
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                >
                    <div v-if="completed && !isLoading" class="w-full max-w-lg text-center">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-green-500 shadow-lg shadow-green-500/25"
                        >
                            <svg
                                class="h-10 w-10 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                        <h2
                            class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl"
                        >
                            Welcome to PornTk
                        </h2>
                        <p class="mt-3 text-lg text-gray-500 dark:text-gray-400">
                            Your account is ready. Redirecting you now...
                        </p>
                        <div
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-800/80 px-5 py-2.5 text-sm text-gray-600 dark:text-gray-300"
                        >
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                />
                            </svg>
                            Setting things up
                        </div>
                    </div>
                </Transition>

                <div v-if="isLoading" class="w-full max-w-lg">
                    <div class="mb-8 text-center sm:mb-10">
                        <h1
                            class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl"
                        >
                            Welcome to <span class="text-[#F02C56]">PornTk</span>
                        </h1>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-400 sm:text-lg">
                            Setting up your account...
                        </p>

                        <div
                            class="rounded-3xl mt-10 p-10 border border-gray-200/80 dark:border-gray-700/40 bg-white/90 dark:bg-gray-900/80 shadow-xl shadow-gray-900/5 dark:shadow-black/30 backdrop-blur-md"
                        >
                            <Spinner />
                        </div>
                    </div>
                </div>

                <Transition
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                >
                    <div v-if="invalidLink && !isLoading" class="w-full max-w-lg text-center">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-red-400 to-red-500 shadow-lg shadow-red-500/25"
                        >
                            <svg
                                class="h-10 w-10 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </div>
                        <h2
                            class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl"
                        >
                            Invalid or Expired Link
                        </h2>
                        <p class="mt-3 text-lg text-gray-500 dark:text-gray-400">
                            This invitation link is no longer valid. It may have expired or already
                            been used.
                        </p>
                        <router-link
                            to="/"
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-800/80 px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700/80 transition-colors"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                            Back to home
                        </router-link>
                    </div>
                </Transition>

                <div v-if="!completed && !isLoading && !invalidLink" class="w-full max-w-lg">
                    <div class="mb-8 text-center sm:mb-10">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border border-gray-200 dark:border-gray-700/60 bg-white/70 dark:bg-gray-800/50 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 backdrop-blur-sm"
                        >
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"
                                ></span>
                                <span
                                    class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"
                                ></span>
                            </span>
                            Application Approved
                        </div>
                        <h1
                            class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl"
                        >
                            Welcome to <span class="text-[#F02C56]">PornTk</span>
                        </h1>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-400 sm:text-lg">
                            Let's finish setting up your account.
                        </p>
                    </div>

                    <div
                        class="rounded-3xl border border-gray-200/80 dark:border-gray-700/40 bg-white/90 dark:bg-gray-900/80 shadow-xl shadow-gray-900/5 dark:shadow-black/30 backdrop-blur-md"
                    >
                        <div class="flex items-center gap-1.5 px-6 pt-6 sm:px-8">
                            <template v-for="i in totalSteps" :key="i">
                                <div
                                    class="h-1.5 flex-1 rounded-full transition-all duration-500 ease-out"
                                    :class="
                                        i <= currentStep
                                            ? 'bg-[#F02C56]'
                                            : 'bg-gray-200 dark:bg-gray-700/50'
                                    "
                                />
                            </template>
                        </div>

                        <div class="px-6 py-6 sm:px-8 sm:py-8">
                            <p
                                class="mb-1 text-xs font-semibold uppercase tracking-widest text-[#F02C56]"
                            >
                                Step {{ currentStep }} of {{ totalSteps }}
                            </p>

                            <div class="relative">
                                <TransitionGroup
                                    :enter-active-class="
                                        slideDirection === 'forward'
                                            ? 'transition-all duration-400 ease-out'
                                            : 'transition-all duration-400 ease-out'
                                    "
                                    :enter-from-class="
                                        slideDirection === 'forward'
                                            ? 'opacity-0 translate-x-8'
                                            : 'opacity-0 -translate-x-8'
                                    "
                                    enter-to-class="opacity-100 translate-x-0"
                                    :leave-active-class="'transition-all duration-300 ease-in absolute inset-x-0'"
                                    :leave-to-class="
                                        slideDirection === 'forward'
                                            ? 'opacity-0 -translate-x-8'
                                            : 'opacity-0 translate-x-8'
                                    "
                                >
                                    <div v-if="currentStep === verifyStep" :key="'step-verify'">
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            Verify your invitation
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Confirm your identity to activate your approved account.
                                        </p>

                                        <div class="mt-6 space-y-5">
                                            <div
                                                class="flex items-center gap-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/50 p-4"
                                            >
                                                <div
                                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-[#F02C56]/10 dark:bg-[#F02C56]/20"
                                                >
                                                    <svg
                                                        class="h-5 w-5 text-[#F02C56]"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                                                        />
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                                    >
                                                        {{ inviteEmail }}
                                                    </p>
                                                    <p
                                                        class="text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        Approved email address
                                                    </p>
                                                </div>
                                            </div>

                                            <template v-if="hasCaptcha">
                                                <template
                                                    v-if="appCaptcha.provider === 'turnstile'"
                                                >
                                                    <CloudflareTurnstile
                                                        ref="turnstileRef"
                                                        :sitekey="appCaptcha.siteKey"
                                                        theme="auto"
                                                        @success="onCaptchaSuccess"
                                                        @error="onCaptchaError"
                                                        @expired="onCaptchaExpired"
                                                    />
                                                </template>
                                                <template
                                                    v-else-if="appCaptcha.provider === 'hcaptcha'"
                                                >
                                                    <HCaptcha
                                                        ref="hcaptchaRef"
                                                        :sitekey="appCaptcha.siteKey"
                                                        theme="auto"
                                                        @success="onCaptchaSuccess"
                                                        @error="onCaptchaError"
                                                        @expired="onCaptchaExpired"
                                                    />
                                                </template>
                                            </template>

                                            <AnimatedButton
                                                variant="primaryGradient"
                                                size="lg"
                                                :pill="true"
                                                :loading="verifyLoading"
                                                :disabled="hasCaptcha && !captchaToken"
                                                class="w-full"
                                                @click="handleVerifyInvite"
                                            >
                                                Verify Invitation
                                            </AnimatedButton>
                                        </div>
                                    </div>

                                    <div v-if="currentStep === usernameStep" :key="'step-username'">
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            Choose your username
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            This is how others will find and mention you.
                                        </p>

                                        <div class="mt-6 space-y-5">
                                            <div>
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    Username
                                                </label>
                                                <div class="relative">
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 left-0 z-10 flex items-center pl-4"
                                                    >
                                                        <span
                                                            class="text-base font-semibold text-gray-400 dark:text-gray-500"
                                                            >@</span
                                                        >
                                                    </div>
                                                    <input
                                                        v-model="form.username"
                                                        type="text"
                                                        required
                                                        maxlength="24"
                                                        placeholder="yourname"
                                                        class="form-input !pl-10 !pr-12"
                                                        @input="debouncedCheckUsername"
                                                    />
                                                    <div
                                                        class="absolute inset-y-0 right-0 flex items-center pr-4"
                                                    >
                                                        <svg
                                                            v-if="usernameLoading"
                                                            class="h-5 w-5 animate-spin text-gray-400"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <circle
                                                                class="opacity-25"
                                                                cx="12"
                                                                cy="12"
                                                                r="10"
                                                                stroke="currentColor"
                                                                stroke-width="4"
                                                            />
                                                            <path
                                                                class="opacity-75"
                                                                fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                                            />
                                                        </svg>
                                                        <svg
                                                            v-else-if="usernameAvailable === true"
                                                            class="h-5 w-5 text-emerald-500"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 13l4 4L19 7"
                                                            />
                                                        </svg>
                                                        <svg
                                                            v-else-if="usernameAvailable === false"
                                                            class="h-5 w-5 text-red-500"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12"
                                                            />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <Transition
                                                    enter-active-class="transition duration-200 ease-out"
                                                    enter-from-class="opacity-0 -translate-y-1"
                                                    enter-to-class="opacity-100 translate-y-0"
                                                    leave-active-class="transition duration-150 ease-in"
                                                    leave-from-class="opacity-100"
                                                    leave-to-class="opacity-0"
                                                >
                                                    <p
                                                        v-if="
                                                            usernameAvailable === true &&
                                                            form.username.length >= 2
                                                        "
                                                        class="mt-2 flex items-center gap-1.5 text-sm font-medium text-emerald-500"
                                                    >
                                                        <svg
                                                            class="h-4 w-4 flex-shrink-0"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 13l4 4L19 7"
                                                            />
                                                        </svg>
                                                        Username is available
                                                    </p>
                                                </Transition>
                                                <Transition
                                                    enter-active-class="transition duration-200 ease-out"
                                                    enter-from-class="opacity-0 -translate-y-1"
                                                    enter-to-class="opacity-100 translate-y-0"
                                                    leave-active-class="transition duration-150 ease-in"
                                                    leave-from-class="opacity-100"
                                                    leave-to-class="opacity-0"
                                                >
                                                    <p
                                                        v-if="usernameAvailable === false"
                                                        class="mt-2 flex items-center gap-1.5 text-sm font-medium text-red-500"
                                                    >
                                                        <svg
                                                            class="h-4 w-4 flex-shrink-0"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
                                                            />
                                                        </svg>
                                                        {{
                                                            usernameError ||
                                                            'Username is already taken'
                                                        }}
                                                    </p>
                                                </Transition>
                                                <p
                                                    v-if="suggestedUsername"
                                                    class="mt-2 text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Based on your application:
                                                    <button
                                                        type="button"
                                                        @click="applySuggestedUsername"
                                                        class="ml-1 font-semibold text-[#F02C56] hover:text-[#D7284A] transition-colors cursor-pointer"
                                                    >
                                                        @{{ suggestedUsername }}
                                                    </button>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="currentStep === passwordStep" :key="'step-password'">
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            Secure your account
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Create a strong password to protect your account.
                                        </p>

                                        <div class="mt-6 space-y-5">
                                            <div>
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    Password
                                                </label>
                                                <div class="relative">
                                                    <input
                                                        v-model="form.password"
                                                        :type="showPassword ? 'text' : 'password'"
                                                        required
                                                        placeholder="Create a strong password"
                                                        class="form-input !pr-12"
                                                    />
                                                    <button
                                                        type="button"
                                                        @click="showPassword = !showPassword"
                                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors cursor-pointer"
                                                    >
                                                        <EyeIcon
                                                            v-if="showPassword"
                                                            class="h-5 w-5"
                                                        />
                                                        <EyeSlashIcon v-else class="h-5 w-5" />
                                                    </button>
                                                </div>

                                                <div v-if="form.password" class="mt-3">
                                                    <div
                                                        class="flex items-center justify-between text-sm mb-1.5"
                                                    >
                                                        <span
                                                            class="text-gray-600 dark:text-gray-400"
                                                            >Password strength</span
                                                        >
                                                        <span
                                                            class="font-medium"
                                                            :class="passwordStrengthColor"
                                                        >
                                                            {{ passwordStrength.text }}
                                                        </span>
                                                    </div>
                                                    <div
                                                        class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5"
                                                    >
                                                        <div
                                                            class="h-1.5 rounded-full transition-all duration-300"
                                                            :class="passwordStrengthBarColor"
                                                            :style="{
                                                                width: `${(passwordStrength.score / 5) * 100}%`
                                                            }"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    Confirm Password
                                                </label>
                                                <input
                                                    v-model="form.confirmPassword"
                                                    type="password"
                                                    required
                                                    placeholder="Confirm your password"
                                                    class="form-input"
                                                />
                                                <Transition
                                                    enter-active-class="transition duration-200 ease-out"
                                                    enter-from-class="opacity-0 -translate-y-1"
                                                    enter-to-class="opacity-100 translate-y-0"
                                                    leave-active-class="transition duration-150 ease-in"
                                                    leave-from-class="opacity-100"
                                                    leave-to-class="opacity-0"
                                                >
                                                    <p
                                                        v-if="
                                                            form.confirmPassword &&
                                                            form.password !== form.confirmPassword
                                                        "
                                                        class="mt-2 flex items-center gap-1.5 text-sm font-medium text-red-500"
                                                    >
                                                        <svg
                                                            class="h-4 w-4 flex-shrink-0"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
                                                            />
                                                        </svg>
                                                        Passwords do not match
                                                    </p>
                                                </Transition>
                                                <Transition
                                                    enter-active-class="transition duration-200 ease-out"
                                                    enter-from-class="opacity-0 -translate-y-1"
                                                    enter-to-class="opacity-100 translate-y-0"
                                                    leave-active-class="transition duration-150 ease-in"
                                                    leave-from-class="opacity-100"
                                                    leave-to-class="opacity-0"
                                                >
                                                    <p
                                                        v-if="
                                                            form.confirmPassword &&
                                                            form.password ===
                                                                form.confirmPassword &&
                                                            form.password.length > 0
                                                        "
                                                        class="mt-2 flex items-center gap-1.5 text-sm font-medium text-emerald-500"
                                                    >
                                                        <svg
                                                            class="h-4 w-4 flex-shrink-0"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 13l4 4L19 7"
                                                            />
                                                        </svg>
                                                        Passwords match
                                                    </p>
                                                </Transition>
                                            </div>
                                        </div>
                                    </div>
                                </TransitionGroup>
                            </div>

                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="opacity-0 -translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-150"
                                leave-to-class="opacity-0"
                            >
                                <div
                                    v-if="generalError"
                                    class="mt-4 flex items-start gap-2.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-500/20 p-3.5"
                                >
                                    <svg
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                        {{ generalError }}
                                    </p>
                                </div>
                            </Transition>

                            <div
                                v-if="currentStep > verifyStep"
                                class="mt-8 flex items-center gap-3"
                            >
                                <AnimatedButton
                                    v-if="currentStep > usernameStep"
                                    variant="outline"
                                    size="md"
                                    @click="prevStep"
                                    pill
                                >
                                    <div class="flex items-center gap-1.5">
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 19l-7-7 7-7"
                                            />
                                        </svg>
                                        Back
                                    </div>
                                </AnimatedButton>

                                <div class="ml-auto">
                                    <AnimatedButton
                                        v-if="!isLastStep"
                                        variant="primaryGradient"
                                        size="lg"
                                        :pill="true"
                                        :disabled="!canProceed"
                                        @click="nextStep"
                                    >
                                        <div class="flex items-center gap-1.5">
                                            Continue
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7"
                                                />
                                            </svg>
                                        </div>
                                    </AnimatedButton>

                                    <AnimatedButton
                                        v-if="isLastStep"
                                        variant="primaryGradient"
                                        size="lg"
                                        :pill="true"
                                        :disabled="!canProceed"
                                        :loading="loading"
                                        @click="handleSubmit"
                                    >
                                        Create Account
                                    </AnimatedButton>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-3 px-3 flex justify-between items-center gap-3 text-gray-400 dark:text-gray-500"
                    >
                        <router-link to="/" class="flex items-center gap-1 text-blue-500">
                            <HomeIcon class="size-6" />
                        </router-link>

                        <p class="text-center text-xs text-gray-400 dark:text-gray-500">
                            By creating your account, you agree to the <br />
                            <router-link
                                to="/terms"
                                target="_blank"
                                class="text-red-500 font-bold underline"
                                >Terms of Service</router-link
                            >,
                            <router-link
                                to="/privacy"
                                target="_blank"
                                class="text-red-500 font-bold underline"
                                >Privacy Policy</router-link
                            >
                            and
                            <router-link
                                to="/community-guidelines"
                                target="_blank"
                                class="text-red-500 font-bold underline"
                                >Community Guidelines</router-link
                            >.
                        </p>

                        <button @click="handleToggleDarkMode" class="cursor-pointer">
                            <SunIcon v-if="isDark" class="size-6 text-yellow-500" />
                            <MoonIcon v-else class="size-6 text-indigo-400" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </BlankLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AnimatedButton from '@/components/AnimatedButton.vue'
import CloudflareTurnstile from '@/components/Captcha/CloudflareTurnstile.vue'
import HCaptcha from '@/components/Captcha/HCaptcha.vue'
import { EyeIcon, EyeSlashIcon, HomeIcon, MoonIcon, SunIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const axios = inject('axios')
const appCaptcha = inject('appCaptcha')
const authStore = inject('authStore')

const isLoading = ref(true)
const loading = ref(false)
const verifyLoading = ref(false)
const completed = ref(false)
const invalidLink = ref(false)
const generalError = ref(null)
const showPassword = ref(false)
const currentStep = ref(1)
const slideDirection = ref('forward')
const suggestedUsername = ref('')

const usernameLoading = ref(false)
const usernameAvailable = ref(null)
const usernameError = ref('')
let usernameCheckTimer = null

const captchaToken = ref('')
const captchaType = ref(appCaptcha.provider)
const turnstileRef = ref(null)
const hcaptchaRef = ref(null)
const onboardingToken = ref('')

const inviteKey = ref('')
const inviteEmail = ref('')

const form = reactive({
    username: '',
    password: '',
    confirmPassword: ''
})

const isDark = ref(document.documentElement.classList.contains('dark'))

const handleToggleDarkMode = () => {
    isDark.value = !isDark.value
    if (isDark.value) {
        document.documentElement.classList.add('dark')
        localStorage.setItem('theme', 'dark')
    } else {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('theme', 'light')
    }
}

const hasCaptcha = computed(() => appCaptcha?.enabled)

const verifyStep = 1
const usernameStep = 2
const passwordStep = 3

const totalSteps = 3
const isLastStep = computed(() => currentStep.value === totalSteps)

const passwordStrength = computed(() => {
    const password = form.password
    if (!password) return { score: 0, text: '', color: '' }

    let score = 0
    const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        numbers: /\d/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    }

    score = Object.values(checks).filter(Boolean).length

    if (score <= 2) return { score, text: 'Weak', color: 'red' }
    if (score <= 3) return { score, text: 'Fair', color: 'yellow' }
    if (score <= 4) return { score, text: 'Good', color: 'blue' }
    return { score, text: 'Strong', color: 'green' }
})

const passwordStrengthColor = computed(() => {
    const c = passwordStrength.value.color
    return {
        'text-red-600 dark:text-red-400': c === 'red',
        'text-yellow-600 dark:text-yellow-400': c === 'yellow',
        'text-blue-600 dark:text-blue-400': c === 'blue',
        'text-green-600 dark:text-green-400': c === 'green'
    }
})

const passwordStrengthBarColor = computed(() => {
    const c = passwordStrength.value.color
    return {
        'bg-red-500': c === 'red',
        'bg-yellow-500': c === 'yellow',
        'bg-blue-500': c === 'blue',
        'bg-green-500': c === 'green'
    }
})

const canProceed = computed(() => {
    if (currentStep.value === usernameStep) {
        return (
            form.username.length >= 2 && usernameAvailable.value === true && !usernameLoading.value
        )
    }
    if (currentStep.value === passwordStep) {
        return (
            form.password.length >= 8 &&
            form.password === form.confirmPassword &&
            passwordStrength.value.score >= 3
        )
    }
    return true
})

const onCaptchaSuccess = (t) => {
    captchaToken.value = t
}

const onCaptchaError = () => {
    captchaToken.value = ''
    resetCaptcha()
}

const onCaptchaExpired = () => {
    captchaToken.value = ''
    resetCaptcha()
}

const resetCaptcha = () => {
    if (appCaptcha?.provider === 'turnstile') {
        turnstileRef.value?.reset()
    } else if (appCaptcha?.provider === 'hcaptcha') {
        hcaptchaRef.value?.reset()
    }
}

function sanitizeUsername(val) {
    return val
        .toLowerCase()
        .replace(/[^a-z0-9._]/g, '')
        .slice(0, 24)
}

function debouncedCheckUsername() {
    form.username = sanitizeUsername(form.username)
    usernameAvailable.value = null
    usernameError.value = ''

    if (usernameCheckTimer) clearTimeout(usernameCheckTimer)

    if (form.username.length < 2) {
        usernameLoading.value = false
        return
    }

    usernameLoading.value = true
    usernameCheckTimer = setTimeout(() => {
        checkUsernameAvailability()
    }, 500)
}

async function checkUsernameAvailability() {
    try {
        const res = await axios.post('/api/v1/onboarding/username-check', {
            username: form.username,
            onboarding_token: onboardingToken.value
        })
        usernameAvailable.value = res.data?.available ?? false
        usernameError.value = res.data?.message || ''
    } catch (e) {
        if (e.response?.status === 401) {
            generalError.value = 'Session expired. Please refresh and try again.'
            currentStep.value = verifyStep
            onboardingToken.value = ''
            resetCaptcha()
            return
        }
        usernameAvailable.value = false
        usernameError.value = e.response?.data?.message || 'Unable to check username availability'
    } finally {
        usernameLoading.value = false
    }
}

function applySuggestedUsername() {
    form.username = suggestedUsername.value
    debouncedCheckUsername()
}

function nextStep() {
    if (!canProceed.value) return
    generalError.value = null
    slideDirection.value = 'forward'
    currentStep.value = Math.min(currentStep.value + 1, totalSteps)
}

function prevStep() {
    generalError.value = null
    slideDirection.value = 'backward'
    currentStep.value = Math.max(currentStep.value - 1, usernameStep)
}

async function handleVerifyInvite() {
    generalError.value = null
    verifyLoading.value = true

    try {
        const res = await axios.post('/api/v1/onboarding/verify-invite', {
            email: inviteEmail.value,
            key: inviteKey.value,
            captcha_type: hasCaptcha.value ? captchaType.value : undefined,
            captcha_token: hasCaptcha.value ? captchaToken.value : undefined
        })
        if (res.data?.username_requested) {
            suggestedUsername.value = res.data.username_requested
            form.username = res.data.username_requested
        }
        onboardingToken.value = res.data.onboarding_token
        slideDirection.value = 'forward'
        currentStep.value = usernameStep
        if (form.username) {
            checkUsernameAvailability()
        }
    } catch (e) {
        const status = e.response?.status
        if (status === 404 || status === 410) {
            invalidLink.value = true
        } else {
            generalError.value =
                e.response?.data?.message || 'Verification failed. Please try again.'
            resetCaptcha()
        }
    } finally {
        verifyLoading.value = false
    }
}

async function handleSubmit() {
    if (!canProceed.value) return
    generalError.value = null
    loading.value = true

    try {
        await axios.post('/api/v1/onboarding/complete', {
            onboarding_token: onboardingToken.value,
            username: form.username,
            password: form.password,
            password_confirmation: form.confirmPassword
        })

        completed.value = true

        setTimeout(() => {
            window.location.href = '/'
        }, 2000)
    } catch (e) {
        if (e.response?.status === 401) {
            generalError.value = 'Session expired. Please refresh and start over.'
            currentStep.value = verifyStep
            onboardingToken.value = ''
            resetCaptcha()
        } else if (e.response?.data?.errors) {
            const errors = e.response.data.errors
            const messages = Object.values(errors).flat()
            generalError.value = messages[0] || 'Something went wrong. Please try again.'
        } else {
            generalError.value =
                e.response?.data?.message || 'Something went wrong. Please try again.'
        }
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    if (authStore.isAuthenticated) {
        router.push('/')
        return
    }
    inviteKey.value = route.query.key || ''
    inviteEmail.value = route.query.email || ''

    if (!inviteKey.value || !inviteEmail.value) {
        invalidLink.value = true
    }

    isLoading.value = false
})
</script>
