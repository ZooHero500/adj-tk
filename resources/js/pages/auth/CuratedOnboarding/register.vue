<template>
    <BlankLayout>
        <div class="onboarding relative min-h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-1/2 -left-1/4 h-[900px] w-[900px] rounded-full bg-gradient-to-br from-primary/40 via-primary/20 to-transparent blur-3xl animate-drift-slow"
                />
                <div
                    class="absolute -bottom-1/3 -right-1/4 h-[700px] w-[700px] rounded-full bg-gradient-to-tl from-primary/30 via-rose-400/20 to-transparent blur-3xl animate-drift-slow-reverse"
                />
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-rose-400/20 via-primary/10 to-transparent blur-2xl animate-drift-slow"
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
                    <div v-if="submitted && !isLoading" class="w-full max-w-lg text-center">
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
                            You're in the queue
                        </h2>
                        <p class="mt-3 text-lg text-gray-500 dark:text-gray-400">
                            Check your inbox for a verification link. Once confirmed, our team will
                            review your application.
                        </p>
                        <div
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-800/80 px-5 py-2.5 text-sm text-gray-600 dark:text-gray-300"
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
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                            We'll be in touch soon
                        </div>
                    </div>
                </Transition>

                <div v-if="isLoading" class="w-full max-w-lg">
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
                            Applications Open
                        </div>
                        <h1
                            class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl"
                        >
                            Join <span class="text-primary">PornTk</span>
                        </h1>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-400 sm:text-lg">
                            A community-first space for short video.
                        </p>

                        <div
                            class="rounded-3xl mt-10 p-10 border border-gray-200/80 dark:border-gray-700/40 bg-white/90 dark:bg-gray-900/80 shadow-xl shadow-gray-900/5 dark:shadow-black/30 backdrop-blur-md"
                        >
                            <Spinner />
                        </div>
                    </div>
                </div>

                <div v-if="!submitted && !isLoading" class="w-full max-w-lg">
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
                            Applications Open
                        </div>
                        <h1
                            class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl"
                        >
                            Join <span class="text-primary">PornTk</span>
                        </h1>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-400 sm:text-lg">
                            A community-first space for short video.
                        </p>
                    </div>

                    <div
                        v-if="guidelines"
                        class="mb-6 rounded-2xl border border-gray-200 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/50 p-5"
                    >
                        <div class="flex items-start gap-3">
                            <InformationCircleIcon
                                class="w-5 h-5 mt-0.5 shrink-0 text-gray-400 dark:text-gray-500"
                            />
                            <div
                                v-html="guidelines"
                                class="prose prose-sm prose-gray dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 [&>*:first-child]:mt-0 [&>*:last-child]:mb-0"
                            />
                        </div>
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
                                            ? 'bg-primary'
                                            : 'bg-gray-200 dark:bg-gray-700/50'
                                    "
                                />
                            </template>
                        </div>

                        <div class="px-6 py-6 sm:px-8 sm:py-8">
                            <p
                                class="mb-1 text-xs font-semibold uppercase tracking-widest text-primary"
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
                                    <div
                                        v-if="currentStep === captchaStep && hasCaptcha"
                                        :key="'step-captcha'"
                                    >
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            Quick verification
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Complete the challenge below to continue.
                                        </p>

                                        <div class="mt-6">
                                            <template v-if="appCaptcha.provider === 'turnstile'">
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
                                        </div>
                                    </div>

                                    <div v-if="currentStep === ageStep" :key="'step-age'">
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            First, verify your age
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            We need to make sure you meet the minimum age
                                            requirement.
                                        </p>

                                        <div class="mt-6">
                                            <label
                                                class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                            >
                                                Date of Birth
                                            </label>
                                            <input
                                                v-model="form.birthdate"
                                                type="date"
                                                required
                                                :max="maxBirthdate"
                                                @input="checkAge"
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
                                                    v-if="ageWarning"
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
                                                    {{ ageWarning }}
                                                </p>
                                            </Transition>
                                            <FieldError
                                                v-if="!ageWarning"
                                                :errors="validationErrors"
                                                field="birthdate"
                                            />
                                        </div>
                                    </div>

                                    <div v-if="currentStep === identityStep" :key="'step-identity'">
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            Set up your identity
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Where should we reach you, and what should we call you?
                                        </p>

                                        <div class="mt-6 space-y-5">
                                            <div>
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    Email Address
                                                </label>
                                                <div class="relative">
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 left-0 z-10 flex items-center pl-4"
                                                    >
                                                        <svg
                                                            class="h-5 w-5 text-gray-400 dark:text-gray-500"
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
                                                    <input
                                                        v-model="form.email"
                                                        type="email"
                                                        required
                                                        placeholder="you@example.com"
                                                        class="form-input !pl-12"
                                                    />
                                                </div>
                                                <FieldError
                                                    :errors="validationErrors"
                                                    field="email"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    Preferred Username
                                                    <span class="font-normal text-gray-400"
                                                        >(optional)</span
                                                    >
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
                                                        v-model="form.username_requested"
                                                        type="text"
                                                        placeholder="yourname"
                                                        maxlength="24"
                                                        class="form-input !pl-10"
                                                        :class="
                                                            usernameError
                                                                ? 'border-red-400 dark:border-red-500 focus:ring-red-400'
                                                                : ''
                                                        "
                                                    />
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
                                                        v-if="usernameError"
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
                                                        {{ usernameError }}
                                                    </p>
                                                </Transition>
                                                <FieldError
                                                    :errors="validationErrors"
                                                    field="username_requested"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="currentStep === aboutStep" :key="'step-about'">
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            Tell us about you
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Help us understand what brings you to the fediverse.
                                        </p>

                                        <div class="mt-6 space-y-5">
                                            <div>
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    Why do you want to join?
                                                </label>
                                                <div class="relative">
                                                    <textarea
                                                        v-model="form.reason"
                                                        required
                                                        rows="4"
                                                        minlength="10"
                                                        maxlength="2000"
                                                        placeholder="I want to share my creative work with a community that values authenticity..."
                                                        class="form-input resize-none !pb-8"
                                                    />
                                                    <div
                                                        class="absolute bottom-2.5 right-3 text-xs tabular-nums text-gray-400/80"
                                                    >
                                                        {{ form.reason.length
                                                        }}<span
                                                            class="text-gray-300 dark:text-gray-600"
                                                            >/2000</span
                                                        >
                                                    </div>
                                                </div>
                                                <FieldError
                                                    :errors="validationErrors"
                                                    field="reason"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    Existing Fediverse Account
                                                    <span class="font-normal text-gray-400"
                                                        >(optional)</span
                                                    >
                                                </label>
                                                <div class="relative">
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 left-0 z-10 flex items-center pl-4"
                                                    >
                                                        <svg
                                                            class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"
                                                            />
                                                        </svg>
                                                    </div>
                                                    <input
                                                        v-model="form.fediverse_account"
                                                        type="text"
                                                        placeholder="@user@mastodon.social"
                                                        class="form-input !pl-12"
                                                        :class="
                                                            fediverseError
                                                                ? 'border-red-400 dark:border-red-500 focus:ring-red-400'
                                                                : ''
                                                        "
                                                    />
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
                                                        v-if="fediverseError"
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
                                                        {{ fediverseError }}
                                                    </p>
                                                </Transition>
                                                <FieldError
                                                    :errors="validationErrors"
                                                    field="fediverse_account"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="currentStep === customStep && hasCustomQuestions"
                                        :key="'step-custom'"
                                    >
                                        <h2
                                            class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
                                        >
                                            A few more things
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            The instance admin has a couple of extra questions.
                                        </p>

                                        <div class="mt-6 space-y-5">
                                            <div
                                                v-for="question in customQuestions"
                                                :key="question.id"
                                            >
                                                <label
                                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                >
                                                    {{ question.label }}
                                                    <span
                                                        v-if="question.required"
                                                        class="text-red-400"
                                                        >*</span
                                                    >
                                                    <span v-else class="font-normal text-gray-400"
                                                        >(optional)</span
                                                    >
                                                </label>

                                                <select
                                                    v-if="question.type === 'select'"
                                                    v-model="form.custom_answers[question.id]"
                                                    :required="question.required"
                                                    class="form-input"
                                                    :class="
                                                        !form.custom_answers[question.id]
                                                            ? 'text-gray-400'
                                                            : ''
                                                    "
                                                >
                                                    <option value="" disabled selected>
                                                        Select an option...
                                                    </option>
                                                    <option
                                                        v-for="opt in question.options"
                                                        :key="opt"
                                                        :value="opt"
                                                    >
                                                        {{ opt }}
                                                    </option>
                                                </select>

                                                <div
                                                    v-else-if="question.type === 'textarea'"
                                                    class="relative"
                                                >
                                                    <textarea
                                                        v-model="form.custom_answers[question.id]"
                                                        :required="question.required"
                                                        :placeholder="question.label"
                                                        rows="3"
                                                        maxlength="1000"
                                                        class="form-input resize-none !pb-8"
                                                    />
                                                    <div
                                                        class="absolute bottom-2.5 right-3 text-xs tabular-nums text-gray-400/80"
                                                    >
                                                        {{
                                                            form.custom_answers[question.id]
                                                                ?.length || 0
                                                        }}<span
                                                            class="text-gray-300 dark:text-gray-600"
                                                            >/1000</span
                                                        >
                                                    </div>
                                                </div>

                                                <div v-else class="relative">
                                                    <input
                                                        v-model="form.custom_answers[question.id]"
                                                        type="text"
                                                        :placeholder="question.label"
                                                        :required="question.required"
                                                        maxlength="140"
                                                        class="form-input !pr-16"
                                                    />
                                                    <div
                                                        class="absolute inset-y-0 right-3 flex items-center text-xs tabular-nums text-gray-400/80"
                                                    >
                                                        {{
                                                            form.custom_answers[question.id]
                                                                ?.length || 0
                                                        }}<span
                                                            class="text-gray-300 dark:text-gray-600"
                                                            >/140</span
                                                        >
                                                    </div>
                                                </div>

                                                <FieldError
                                                    :errors="validationErrors"
                                                    :field="`custom_answers.${question.id}`"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </TransitionGroup>
                            </div>

                            <input
                                v-model="form.website"
                                type="text"
                                name="website"
                                autocomplete="off"
                                tabindex="-1"
                                class="absolute opacity-0 pointer-events-none h-0 w-0"
                            />

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

                            <div class="mt-8 flex items-center gap-3">
                                <AnimatedButton
                                    v-if="currentStep > 1"
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
                                        Submit Application
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
                            By creating a new account on our service, you agree to the <br />
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
import { ref, reactive, computed, onMounted, watch, inject } from 'vue'
import { useCuratedOnboarding } from '@/composables/useCuratedOnboarding'
import FieldError from '@/components/FieldError.vue'
import AnimatedButton from '@/components/AnimatedButton.vue'
import CloudflareTurnstile from '@/components/Captcha/CloudflareTurnstile.vue'
import HCaptcha from '@/components/Captcha/HCaptcha.vue'
import {
    ChevronLeftIcon,
    HomeIcon,
    InformationCircleIcon,
    MoonIcon,
    SunIcon
} from '@heroicons/vue/24/outline'
import { useRouter } from 'vue-router'

const { loading, fetchConfig, submitApplication, customQuestions, guidelines, minAge } =
    useCuratedOnboarding()

const router = useRouter()
const appCaptcha = inject('appCaptcha')
const authStore = inject('authStore')
const appConfig = inject('appConfig')
const submitted = ref(false)
const isLoading = ref(true)
const ageWarning = ref(null)
const generalError = ref(null)
const validationErrors = ref({})
const formLoadedAt = ref(null)
const currentStep = ref(1)
const slideDirection = ref('forward')

const captchaToken = ref('')
const captchaType = ref(appCaptcha.provider)
const turnstileRef = ref(null)
const hcaptchaRef = ref(null)

const form = reactive({
    email: '',
    username_requested: '',
    birthdate: '',
    reason: '',
    fediverse_account: '',
    custom_answers: {},
    website: ''
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

const maxBirthdate = new Date().toISOString().split('T')[0]

const hasCaptcha = computed(() => appCaptcha?.enabled)
const hasCustomQuestions = computed(() => customQuestions.value?.length > 0)

const captchaStep = computed(() => (hasCaptcha.value ? 1 : -1))
const ageStep = computed(() => (hasCaptcha.value ? 2 : 1))
const identityStep = computed(() => (hasCaptcha.value ? 3 : 2))
const aboutStep = computed(() => (hasCaptcha.value ? 4 : 3))
const customStep = computed(() => (hasCustomQuestions.value ? aboutStep.value + 1 : -1))

const totalSteps = computed(() => {
    let steps = 3
    if (hasCaptcha.value) steps++
    if (hasCustomQuestions.value) steps++
    return steps
})

const isLastStep = computed(() => currentStep.value === totalSteps.value)

const usernameError = computed(() => {
    const v = form.username_requested
    if (!v) return null
    if (v.length < 3) return 'Username must be at least 3 characters'
    if (!/^[a-zA-Z0-9_.-]+$/.test(v))
        return 'Only letters, numbers, underscores, hyphens, and periods are allowed'
    if (/^[._-]/.test(v)) return 'Username cannot start with a period, underscore, or hyphen'
    if (/[.-]$/.test(v)) return 'Username cannot end with a period or hyphen'
    if (/--/.test(v)) return 'Username cannot contain consecutive hyphens'
    if (/\.\./.test(v)) return 'Username cannot contain consecutive periods'
    return null
})

const fediverseError = computed(() => {
    const v = form.fediverse_account
    if (!v) return null
    if (!/^@[a-zA-Z0-9_.-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v)) {
        return 'Must be in the format @username@domain.tld'
    }
    return null
})

const canProceed = computed(() => {
    if (currentStep.value === captchaStep.value && hasCaptcha.value) {
        return !!captchaToken.value
    }
    if (currentStep.value === ageStep.value) {
        return form.birthdate && !ageWarning.value
    }
    if (currentStep.value === identityStep.value) {
        return form.email && form.email.includes('@') && !usernameError.value
    }
    if (currentStep.value === aboutStep.value) {
        return form.reason && form.reason.length >= 10 && !fediverseError.value
    }
    if (currentStep.value === customStep.value && hasCustomQuestions.value) {
        return customQuestions.value.every((q) => {
            if (!q.required) return true
            const answer = form.custom_answers[q.id]
            return answer && answer.trim().length > 0
        })
    }
    return true
})

const onCaptchaSuccess = (t) => {
    captchaToken.value = t
}

const onCaptchaError = () => {
    captchaToken.value = ''
}

const onCaptchaExpired = () => {
    captchaToken.value = ''
}

const resetCaptcha = () => {
    captchaToken.value = ''
    if (appCaptcha?.provider === 'turnstile') {
        turnstileRef.value?.reset()
    } else if (appCaptcha?.provider === 'hcaptcha') {
        hcaptchaRef.value?.reset()
    }
}

function checkAge() {
    if (!form.birthdate || !minAge.value) {
        ageWarning.value = null
        return
    }

    const birthdate = new Date(form.birthdate)
    const minDate = new Date('1900-01-01')

    if (birthdate < minDate) {
        ageWarning.value = 'Date of birth must be after January 1, 1900.'
        return
    }

    const today = new Date()
    let age = today.getFullYear() - birthdate.getFullYear()
    const monthDiff = today.getMonth() - birthdate.getMonth()

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
        age--
    }

    ageWarning.value =
        age < minAge.value ? `You must be at least ${minAge.value} years old to apply.` : null
}

function nextStep() {
    if (!canProceed.value) return
    slideDirection.value = 'forward'
    currentStep.value = Math.min(currentStep.value + 1, totalSteps.value)
    generalError.value = null
}

function prevStep() {
    slideDirection.value = 'backward'
    currentStep.value = Math.max(currentStep.value - 1, 1)
}

async function handleSubmit() {
    if (!canProceed.value) return
    generalError.value = null
    validationErrors.value = {}

    try {
        const payload = {
            ...form,
            _loaded_at: formLoadedAt.value
        }

        if (hasCaptcha.value) {
            payload.captcha_type = captchaType.value
            payload.captcha_token = captchaToken.value
        }

        await submitApplication(payload)
        submitted.value = true
    } catch (e) {
        if (e.errors) {
            validationErrors.value = e.errors
            const errorFields = Object.keys(e.errors)
            let earliestErrorStep = totalSteps.value

            if (errorFields.some((f) => f.startsWith('captcha_token')) && hasCaptcha.value) {
                generalError.value = 'Captcha verification failed, please try again'
                earliestErrorStep = Math.min(earliestErrorStep, captchaStep.value)
            }

            if (errorFields.some((f) => f === 'birthdate')) {
                ageWarning.value = e.errors.birthdate?.[0] || 'Invalid date of birth'
                earliestErrorStep = Math.min(earliestErrorStep, ageStep.value)
            }

            if (errorFields.some((f) => f === 'email' || f === 'username_requested')) {
                earliestErrorStep = Math.min(earliestErrorStep, identityStep.value)
            }

            if (errorFields.some((f) => f === 'reason' || f === 'fediverse_account')) {
                earliestErrorStep = Math.min(earliestErrorStep, aboutStep.value)
            }

            if (
                errorFields.some((f) => f.startsWith('custom_answers')) &&
                hasCustomQuestions.value
            ) {
                earliestErrorStep = Math.min(earliestErrorStep, customStep.value)
            }

            currentStep.value = earliestErrorStep
        } else {
            generalError.value = e.message || 'Something went wrong. Please try again.'
            if (hasCaptcha.value) {
                currentStep.value = captchaStep.value
            }
        }

        if (hasCaptcha.value) {
            resetCaptcha()
        }
    }
}

watch(
    () => customQuestions.value,
    (questions) => {
        form.custom_answers = Object.fromEntries(
            questions.map((q) => [q.id, form.custom_answers[q.id] ?? ''])
        )
    },
    { immediate: true }
)

onMounted(async () => {
    if (appConfig.registration_mode !== 'curated') {
        router.push('/register')
        return
    }

    if (authStore.isAuthenticated) {
        router.push('/')
        return
    }

    await fetchConfig().then(() => {
        setTimeout(() => {
            isLoading.value = false
            formLoadedAt.value = Math.floor(Date.now() / 1000)
        }, 1000)
    })
})
</script>
