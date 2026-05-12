<template>
    <div>
        <div v-if="initialLoading" class="mt-1.5">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                Onboarding Settings
            </h1>
            <div
                class="flex flex-col justify-center items-center gap-3 text-center py-12 text-gray-500 dark:text-gray-400"
            >
                <Spinner />
                <p>Loading settings...</p>
            </div>
        </div>

        <div v-else class="space-y-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Onboarding Settings
                </h1>
                <div class="flex items-center justify-between gap-3">
                    <p
                        v-if="saveMessage"
                        class="text-sm"
                        :class="saveError ? 'text-red-600' : 'text-green-600 dark:text-green-400'"
                    >
                        {{ saveMessage }}
                    </p>
                    <div v-else></div>
                    <AnimatedButton @click="handleSave" :disabled="saving">
                        {{ saving ? 'Saving...' : 'Save Settings' }}
                    </AnimatedButton>
                </div>
            </div>

            <template v-if="form.registration_mode === 'curated'">
                <div class="flex flex-col lg:flex-row gap-3">
                    <CuratedSettingsCard title="Age Requirement">
                        <div class="flex flex-col justify-start items-start gap-3">
                            <label
                                class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer"
                            >
                                <input
                                    v-model="hasAgeRequirement"
                                    type="checkbox"
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                                />
                                Require minimum age
                            </label>
                            <div v-if="hasAgeRequirement" class="w-full flex items-center gap-3">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Minimum</span
                                >
                                <input
                                    v-model.number="form.min_age"
                                    type="number"
                                    min="13"
                                    max="25"
                                    class="flex-grow-1 w-20 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-gray-900 dark:text-white"
                                />
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >years old</span
                                >
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            Applicants under this age will be automatically rejected.
                        </p>
                    </CuratedSettingsCard>

                    <CuratedSettingsCard title="Application Guidelines" class="flex-grow-1">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            Shown at the top of the application form. Describe your community and
                            what you're looking for.
                        </p>
                        <TiptapEditor v-model="form.guidelines" :maxlength="2000" />
                        <div
                            class="flex justify-end text-xs text-gray-500 dark:text-gray-400 mt-1.5"
                        >
                            <span>{{ form.guidelines?.length || 0 }} / 2000</span>
                        </div>
                    </CuratedSettingsCard>
                </div>

                <div class="flex flex-col lg:flex-row gap-3">
                    <CuratedSettingsCard title="Custom Questions" class="min-w-[50%]">
                        <template #header-action>
                            <button
                                @click="showAddQuestion = true"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
                            >
                                + Add question
                            </button>
                        </template>

                        <div
                            v-if="!questions.length"
                            class="text-sm text-gray-400 py-6 text-center"
                        >
                            No custom questions yet. Add one to collect additional info from
                            applicants.
                        </div>

                        <div v-else ref="questionListRef" class="space-y-2">
                            <div
                                v-for="question in questions"
                                :key="question.id"
                                :data-id="question.id"
                                class="group flex items-start gap-2 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 transition-colors"
                                :class="{
                                    'ring-2 ring-blue-500': editingQuestion?.id === question.id
                                }"
                            >
                                <button
                                    class="drag-handle mt-1 cursor-grab text-gray-300 dark:text-gray-600 hover:text-gray-500 dark:hover:text-gray-400"
                                    title="Drag to reorder"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-12a2 2 0 10.001 4.001A2 2 0 0013 2zm0 6a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z"
                                        />
                                    </svg>
                                </button>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                            >{{ question.label }}</span
                                        >
                                        <span
                                            class="shrink-0 px-1.5 py-0.5 text-[10px] uppercase tracking-wider font-medium rounded bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400"
                                            >{{ questionTypeLabel(question.type) }}</span
                                        >
                                        <span
                                            v-if="question.required"
                                            class="shrink-0 px-1.5 py-0.5 text-[10px] uppercase tracking-wider font-medium rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300"
                                            >Required</span
                                        >
                                    </div>
                                    <div
                                        v-if="
                                            question.type === 'select' && question.options?.length
                                        "
                                        class="mt-1 text-xs text-gray-400"
                                    >
                                        Options: {{ question.options.join(', ') }}
                                    </div>
                                </div>
                                <div
                                    class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <button
                                        @click="startEditQuestion(question)"
                                        class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                        title="Edit"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="handleDeleteQuestion(question)"
                                        :disabled="questionLoading === question.id"
                                        class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                        title="Remove"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </CuratedSettingsCard>

                    <CuratedSettingsTabCard title="Email Templates">
                        <template #tabs>
                            <div
                                class="flex w-full items-center gap-0 border-b border-gray-200 dark:border-gray-700/60"
                            >
                                <button
                                    v-for="f in EMAIL_TEMPLATE_FILTERS"
                                    :key="f.key"
                                    @click="activeFilter = f.key"
                                    class="relative flex-1 items-center gap-2 text-sm px-4 py-3.5 transition-colors"
                                    :class="
                                        activeFilter === f.key
                                            ? 'text-gray-900 dark:text-white font-medium'
                                            : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'
                                    "
                                >
                                    {{ f.label }}
                                    <span
                                        v-if="activeFilter === f.key"
                                        class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-t-full"
                                    />
                                </button>
                            </div>
                        </template>

                        <p
                            v-show="['approval', 'rejection'].includes(activeFilter)"
                            class="text-sm text-gray-500 dark:text-gray-400"
                        >
                            Customize the emails sent to applicants. The first line will become the
                            greeting, so make it count. You can use
                            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded"
                                >&lcub;&lcub;email&rcub;&rcub;</code
                            >,
                            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded"
                                >&lcub;&lcub;username&rcub;&rcub;</code
                            >
                            as placeholders. Leave blank to use the default templates.
                        </p>

                        <div class="space-y-4 my-6">
                            <div v-show="activeFilter === 'approval'">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >Approval Email</label
                                >
                                <textarea
                                    v-model="form.approval_template"
                                    rows="6"
                                    placeholder="Welcome to our community! We're glad to have you..."
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div class="flex justify-between items-center text-sm">
                                    <a
                                        href="/admin/curated-onboarding-settings/mail/preview/approved"
                                        class="text-blue-500 font-medium"
                                        >View Preview</a
                                    >
                                    <div class="flex gap-1 text-xs text-gray-400">
                                        <div>{{ form.approval_template?.length || 0 }}</div>
                                        <div>/</div>
                                        <div>5000</div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="activeFilter === 'rejection'">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >Rejection Email</label
                                >
                                <textarea
                                    v-model="form.rejection_template"
                                    rows="6"
                                    placeholder="Thank you for your interest. Unfortunately..."
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div class="flex justify-between items-center text-sm">
                                    <a
                                        href="/admin/curated-onboarding-settings/mail/preview/rejected"
                                        class="text-blue-500 font-medium"
                                        >View Preview</a
                                    >
                                    <div class="flex gap-1 text-xs text-gray-400">
                                        <div>{{ form.rejection_template?.length || 0 }}</div>
                                        <div>/</div>
                                        <div>5000</div>
                                    </div>
                                </div>
                                <div class="mt-2 -mb-[32px]">
                                    <label
                                        class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer"
                                    >
                                        <input
                                            v-model="form.send_rejection_email"
                                            type="checkbox"
                                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                                        />
                                        Send email to applicants when rejected
                                    </label>
                                </div>
                            </div>

                            <div v-show="activeFilter === 'verify'">
                                <div class="flex flex-col gap-3">
                                    <p
                                        class="text-base font-light text-gray-700 dark:text-gray-300"
                                    >
                                        This is the email template we send to applicants after they
                                        submit their application so they can verify their email
                                        address.
                                    </p>
                                    <p
                                        class="text-base font-light text-gray-700 dark:text-gray-300 mb-10"
                                    >
                                        Admins are only notified about a new application after the
                                        applicant successfully verifies their email.
                                    </p>
                                    <AnimatedButton
                                        size="lg"
                                        variant="primaryGradient"
                                        pill
                                        @click="handleEmailReceivedPreview()"
                                    >
                                        View Preview
                                    </AnimatedButton>
                                </div>
                            </div>

                            <div v-show="activeFilter === 'admin'">
                                <div class="flex flex-col gap-3">
                                    <p
                                        class="text-base font-light text-gray-700 dark:text-gray-300 mb-10"
                                    >
                                        This is the email template we send to admins after an
                                        applicant successfully verified their email address, making
                                        the application ready to review.
                                    </p>
                                    <AnimatedButton
                                        size="lg"
                                        variant="primaryGradient"
                                        pill
                                        @click="handleEmailNotifyAdminsPreview()"
                                    >
                                        View Preview
                                    </AnimatedButton>
                                </div>
                            </div>
                        </div>
                    </CuratedSettingsTabCard>
                </div>

                <div class="flex flex-col lg:flex-row gap-3">
                    <CuratedSettingsCard title="Rejection Reasons" class="min-w-[50%]">
                        <template #header-action>
                            <button
                                @click="showAddReason = true"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
                            >
                                + Add reason
                            </button>
                        </template>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            Reusable rejection reasons for admins. The title and note are
                            internal-only. The reason text is sent to the applicant.
                        </p>

                        <div
                            v-if="!rejectionReasons.length"
                            class="text-sm text-gray-400 py-6 text-center"
                        >
                            No rejection reasons yet. Add one to speed up the review process.
                        </div>

                        <div v-else ref="reasonListRef" class="space-y-2">
                            <div
                                v-for="reason in rejectionReasons"
                                :key="reason.id"
                                :data-id="reason.id"
                                class="group flex items-start gap-2 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 transition-colors"
                                :class="{ 'ring-2 ring-blue-500': editingReason?.id === reason.id }"
                            >
                                <button
                                    class="reason-drag-handle mt-1 cursor-grab text-gray-300 dark:text-gray-600 hover:text-gray-500 dark:hover:text-gray-400"
                                    title="Drag to reorder"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-12a2 2 0 10.001 4.001A2 2 0 0013 2zm0 6a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z"
                                        />
                                    </svg>
                                </button>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                            >{{ reason.title }}</span
                                        >
                                        <button
                                            v-if="reason.note"
                                            @click="viewingReason = reason"
                                            class="shrink-0 px-1.5 py-0.5 text-[10px] uppercase tracking-wider font-medium rounded bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                                        >
                                            Note
                                        </button>
                                    </div>
                                    <p
                                        class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate"
                                    >
                                        {{ reason.reason }}
                                    </p>
                                </div>

                                <div
                                    class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <button
                                        @click="startEditReason(reason)"
                                        class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                        title="Edit"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="handleDeleteReason(reason)"
                                        :disabled="reasonLoading === reason.id"
                                        class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                        title="Remove"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </CuratedSettingsCard>

                    <CuratedSettingsCard title="Auto-Expiry" class="flex-grow-1">
                        <div class="flex flex-col space-y-4">
                            <div>
                                <div class="flex items-center gap-3">
                                    <label class="text-sm text-gray-600 dark:text-gray-400 w-[70%]"
                                        >Expire unverified email applicants after</label
                                    >
                                    <input
                                        v-model.number="form.auto_expire_unverified_after"
                                        type="number"
                                        min="0"
                                        max="365"
                                        class="w-20 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-gray-900 dark:text-white"
                                    />
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >days</span
                                    >
                                </div>
                                <p class="text-xs text-gray-400 mt-2">
                                    Set to 0 to disable email verification auto-expiry.
                                </p>
                            </div>
                            <div class="h-[1px] bg-gray-200"></div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <label class="text-sm text-gray-600 dark:text-gray-400 w-[70%]"
                                        >Expire unreviewed applications after</label
                                    >
                                    <input
                                        v-model.number="form.auto_expire_after"
                                        type="number"
                                        min="0"
                                        max="365"
                                        class="w-20 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-gray-900 dark:text-white"
                                    />
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >days</span
                                    >
                                </div>
                                <p class="text-xs text-gray-400 mt-2">
                                    Set to 0 to disable unreviewed application auto-expiry.
                                </p>
                            </div>
                        </div>
                    </CuratedSettingsCard>
                </div>

                <div class="flex flex-col lg:flex-row gap-3">
                    <CuratedSettingsCard title="Admin Email Notify" class="flex-grow-1">
                        <template #header-action>
                            <span class="text-xs text-gray-400"
                                >{{ form.admin_email_send_to.length }} / 10</span
                            >
                        </template>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            Send an email notification when a new applicant successfully verified
                            their email address.
                        </p>

                        <div v-if="form.admin_email_send_to.length" class="space-y-2 mb-3">
                            <div
                                v-for="(entry, index) in form.admin_email_send_to"
                                :key="entry.email"
                                class="flex items-center justify-between gap-2 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <img
                                        v-if="entry.account?.avatar"
                                        :src="entry.account.avatar"
                                        :alt="entry.account.username"
                                        class="w-7 h-7 rounded-full object-cover shrink-0"
                                    />
                                    <div
                                        v-else-if="entry.profile_id"
                                        class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0"
                                    >
                                        <span
                                            class="text-xs font-semibold text-blue-600 dark:text-blue-400"
                                            >{{
                                                (entry.account?.username ||
                                                    entry.email)[0].toUpperCase()
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        v-else
                                        class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 text-gray-400"
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
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            v-if="entry.account?.username"
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                        >
                                            {{ entry.account.username }}
                                            <span class="opacity-50"
                                                >- {{ entry.account?.name }}</span
                                            >
                                        </p>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                        >
                                            {{ entry.email }}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    @click="removeRecipient(index)"
                                    class="shrink-0 p-1 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                    title="Remove"
                                >
                                    <svg
                                        class="w-4 h-4"
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
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="!form.admin_email_send_to.length"
                            class="text-sm text-gray-400 py-4 text-center mb-3"
                        >
                            No recipients added yet.
                        </div>

                        <div v-if="canAddMore" class="space-y-2">
                            <div v-if="filteredAdmins.length" class="relative">
                                <button
                                    @click="showAdminDropdown = !showAdminDropdown"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                >
                                    <span>Add admin account</span>
                                    <svg
                                        class="w-4 h-4 transition-transform"
                                        :class="{ 'rotate-180': showAdminDropdown }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </button>
                                <div
                                    v-if="showAdminDropdown"
                                    class="absolute z-10 mt-1 w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg overflow-auto max-h-[200px]"
                                >
                                    <button
                                        v-for="(admin, index) in filteredAdmins"
                                        :key="admin.id"
                                        @click="addAdminRecipient(admin)"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                        :class="
                                            index !== filteredAdmins.length - 1
                                                ? 'border-b border-gray-200 dark:border-gray-700'
                                                : ''
                                        "
                                    >
                                        <img
                                            v-if="admin?.avatar"
                                            :src="admin.avatar"
                                            :alt="admin.username"
                                            class="w-7 h-7 rounded-full object-cover shrink-0"
                                        />
                                        <div
                                            v-else
                                            class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0"
                                        >
                                            <span
                                                class="text-[10px] font-semibold text-blue-600 dark:text-blue-400"
                                                >{{ admin.username[0].toUpperCase() }}</span
                                            >
                                        </div>
                                        <div class="min-w-0">
                                            <p
                                                class="font-medium text-gray-900 dark:text-white truncate"
                                            >
                                                @{{ admin.username }}
                                                <span class="opacity-50">- {{ admin.name }}</span>
                                            </p>
                                            <p class="text-xs text-gray-400 truncate">
                                                {{ admin.email }}
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <input
                                    v-model="customEmailInput"
                                    type="email"
                                    placeholder="Add a non-admin custom email (ex: address@email.com)"
                                    @keydown.enter.prevent="addCustomEmail"
                                    class="form-input"
                                />
                                <AnimatedButton
                                    variant="primary"
                                    @click="addCustomEmail"
                                    :disabled="!customEmailInput.trim()"
                                    >Add
                                </AnimatedButton>
                            </div>
                        </div>

                        <p v-else class="text-xs text-amber-600 dark:text-amber-400 mt-2">
                            Maximum of 10 recipients reached.
                        </p>
                    </CuratedSettingsCard>
                </div>

                <div class="h-100"></div>
            </template>
        </div>

        <Teleport to="body">
            <div
                v-if="showAddQuestion || editingQuestion"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            >
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-lg mx-4"
                >
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ editingQuestion ? 'Edit Question' : 'Add Question' }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Question Label <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="questionForm.label"
                                type="text"
                                placeholder="e.g., How did you hear about us?"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Answer Type</label
                            >
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    v-for="t in questionTypes"
                                    :key="t.value"
                                    @click="questionForm.type = t.value"
                                    class="px-3 py-2 text-sm rounded-lg border text-center transition-colors"
                                    :class="
                                        questionForm.type === t.value
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium'
                                            : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500'
                                    "
                                >
                                    {{ t.label }}
                                </button>
                            </div>
                        </div>
                        <div v-if="questionForm.type === 'select'">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Dropdown Options</label
                            >
                            <div class="space-y-2">
                                <div
                                    v-for="(opt, idx) in questionForm.options"
                                    :key="idx"
                                    class="flex gap-2"
                                >
                                    <input
                                        v-model="questionForm.options[idx]"
                                        type="text"
                                        :placeholder="`Option ${idx + 1}`"
                                        class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm text-gray-900 dark:text-white"
                                    />
                                    <button
                                        @click="questionForm.options.splice(idx, 1)"
                                        class="p-1.5 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                    >
                                        <svg
                                            class="w-4 h-4"
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
                                    </button>
                                </div>
                                <button
                                    @click="questionForm.options.push('')"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                                >
                                    + Add option
                                </button>
                            </div>
                        </div>
                        <label
                            class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer"
                        >
                            <input
                                v-model="questionForm.required"
                                type="checkbox"
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                            />
                            Required
                        </label>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            @click="closeQuestionModal"
                            class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            Cancel
                        </button>
                        <AnimatedButton
                            @click="handleSaveQuestion"
                            :disabled="!questionForm.label.trim() || questionSaving"
                        >
                            {{ questionSaving ? 'Saving...' : editingQuestion ? 'Update' : 'Add' }}
                        </AnimatedButton>
                    </div>
                </div>
            </div>

            <div
                v-if="showAddReason || editingReason"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            >
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-lg mx-4"
                >
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ editingReason ? 'Edit Rejection Reason' : 'Add Rejection Reason' }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Admin Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="reasonForm.title"
                                type="text"
                                placeholder="e.g., Spam Account"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Only visible to admins. Used to identify this reason quickly.
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Admin Note</label
                            >
                            <textarea
                                v-model="reasonForm.note"
                                rows="2"
                                placeholder="Internal notes about when to use this reason..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Optional. Only visible to admins in this panel.
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Rejection Text <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="reasonForm.reason"
                                rows="3"
                                placeholder="Thank you for applying. Unfortunately your application does not meet our requirements at this time..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                This text is sent to the applicant in the rejection email.
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center gap-2 mt-6">
                        <AnimatedButton
                            v-if="editingReason"
                            variant="primaryOutline"
                            @click="previewReasonMail()"
                        >
                            Preview Mail
                        </AnimatedButton>
                        <div class="flex gap-2">
                            <button
                                @click="closeReasonModal"
                                class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                Cancel
                            </button>
                            <AnimatedButton
                                @click="handleSaveReason"
                                :disabled="
                                    !reasonForm.title.trim() ||
                                    !reasonForm.reason.trim() ||
                                    reasonSaving
                                "
                            >
                                {{ reasonSaving ? 'Saving...' : editingReason ? 'Update' : 'Add' }}
                            </AnimatedButton>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="viewingReason"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            >
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4"
                >
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ viewingReason.title }}
                        </h3>
                        <button
                            @click="viewingReason = null"
                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        >
                            <svg
                                class="w-5 h-5"
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
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div v-if="viewingReason.note">
                            <p
                                class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1"
                            >
                                Admin Note
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ viewingReason.note }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1"
                            >
                                Rejection Text Sent to Applicant
                            </p>
                            <p
                                class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-3 border border-gray-200 dark:border-gray-700"
                            >
                                {{ viewingReason.reason }}
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            @click="onHandleStartEditReason(viewingReason)"
                            class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            Edit
                        </button>
                        <button
                            @click="viewingReason = null"
                            class="px-4 py-2 text-sm rounded-lg bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-200 transition-colors"
                        >
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, nextTick, computed } from 'vue'
import axios from 'axios'
import Sortable from 'sortablejs'
import AnimatedButton from '@/components/AnimatedButton.vue'
import CuratedSettingsCard from '@/components/Admin/CuratedSettingsCard.vue'
import CuratedSettingsTabCard from '@/components/Admin/CuratedSettingsTabCard.vue'
import { useRouter } from 'vue-router'

const API_BASE = '/api/v1/admin/curated-onboarding/settings'

const router = useRouter()
const initialLoading = ref(true)
const saving = ref(false)
const saveMessage = ref('')
const saveError = ref(false)
const hasAgeRequirement = ref(false)
const questions = ref([])
const rejectionReasons = ref([])
const questionListRef = ref(null)
const reasonListRef = ref(null)
const questionLoading = ref(null)
const reasonLoading = ref(null)
const availableAdmins = ref([])
const customEmailInput = ref('')
const showAdminDropdown = ref(false)

const showAddQuestion = ref(false)
const editingQuestion = ref(null)
const questionSaving = ref(false)
const questionForm = reactive({ label: '', type: 'text', required: false, options: [] })

const showAddReason = ref(false)
const editingReason = ref(null)
const viewingReason = ref(null)
const reasonSaving = ref(false)
const reasonForm = reactive({ title: '', note: '', reason: '' })
const activeFilter = ref('approval')

const form = reactive({
    registration_mode: 'open',
    min_age: 16,
    guidelines: '',
    auto_expire_after: 30,
    auto_expire_unverified_after: 30,
    approval_template: '',
    rejection_template: '',
    admin_email_send_to: [],
    send_rejection_email: false
})

const filteredAdmins = computed(() => {
    const usedIds = form.admin_email_send_to.filter((e) => e.profile_id).map((e) => e.profile_id)
    return availableAdmins.value.filter((a) => !usedIds.includes(a.id))
})

const EMAIL_TEMPLATE_FILTERS = [
    { key: 'approval', label: 'Approval' },
    { key: 'rejection', label: 'Rejection' },
    { key: 'verify', label: 'Verification' },
    { key: 'admin', label: 'Admin Notify' }
]

const canAddMore = computed(() => form.admin_email_send_to.length < 10)

const questionTypes = [
    { value: 'text', label: 'Short Text' },
    { value: 'textarea', label: 'Long Text' },
    { value: 'select', label: 'Dropdown' }
]

function addAdminRecipient(admin) {
    if (!canAddMore.value) return
    form.admin_email_send_to.push({
        profile_id: admin.id,
        email: admin.email,
        account: { avatar: admin.avatar, username: admin.username, name: admin.name },
        username: admin.username,
        name: admin.name
    })
    showAdminDropdown.value = false
}

function addCustomEmail() {
    const email = customEmailInput.value.trim()
    if (!email || !canAddMore.value) return
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(email)) return
    const alreadyAdded = form.admin_email_send_to.some(
        (e) => e.email.toLowerCase() === email.toLowerCase()
    )
    if (alreadyAdded) return
    form.admin_email_send_to.push({ profile_id: null, email, username: null, name: null })
    customEmailInput.value = ''
}

function removeRecipient(index) {
    form.admin_email_send_to.splice(index, 1)
}

function onHandleStartEditReason(reason) {
    startEditReason(reason)
    viewingReason.value = null
}

watch(hasAgeRequirement, (val) => {
    if (!val) form.min_age = null
    else form.min_age = form.min_age || 16
})

watch(questions, async () => {
    await nextTick()
    initQuestionSortable()
})
watch(rejectionReasons, async () => {
    await nextTick()
    initReasonSortable()
})

let questionSortable = null
let reasonSortable = null

function initQuestionSortable() {
    if (questionSortable) {
        questionSortable.destroy()
        questionSortable = null
    }

    if (!questionListRef.value) {
        return
    }

    questionSortable = new Sortable(questionListRef.value, {
        animation: 200,
        handle: '.drag-handle',
        ghostClass: 'opacity-30',
        onEnd: async (evt) => {
            const item = questions.value.splice(evt.oldIndex, 1)[0]
            questions.value.splice(evt.newIndex, 0, item)
            try {
                const { data } = await axios.put(`${API_BASE}/questions/reorder`, {
                    order: questions.value.map((q) => q.id)
                })
                questions.value = data.questions
            } catch (e) {
                console.error('Reorder failed:', e)
            }
        }
    })
}

function initReasonSortable() {
    if (reasonSortable) {
        reasonSortable.destroy()
        reasonSortable = null
    }

    if (!reasonListRef.value) {
        return
    }

    reasonSortable = new Sortable(reasonListRef.value, {
        animation: 200,
        handle: '.reason-drag-handle',
        ghostClass: 'opacity-30',
        onEnd: async (evt) => {
            const item = rejectionReasons.value.splice(evt.oldIndex, 1)[0]
            rejectionReasons.value.splice(evt.newIndex, 0, item)
            try {
                const { data } = await axios.put(`${API_BASE}/rejection-reasons/reorder`, {
                    order: rejectionReasons.value.map((r) => r.id)
                })
                rejectionReasons.value = data.rejection_reasons
            } catch (e) {
                console.error('Reorder failed:', e)
            }
        }
    })
}

async function loadSettings() {
    initialLoading.value = true
    try {
        const { data } = await axios.get(API_BASE)
        form.min_age = data.min_age
        form.guidelines = data.guidelines || ''
        form.auto_expire_after = data.auto_expire_after
        form.auto_expire_unverified_after = data.auto_expire_unverified_after
        form.approval_template = data.approval_template || ''
        form.rejection_template = data.rejection_template || ''
        form.send_rejection_email = data.send_rejection_email
        questions.value = data.questions || []
        rejectionReasons.value = data.rejection_reasons || []
        hasAgeRequirement.value = data.min_age !== null
        availableAdmins.value = data.available_admins || []
        form.admin_email_send_to = data.admin_email_send_to || []
        form.registration_mode = 'curated'
    } catch (e) {
    } finally {
        initialLoading.value = false
    }
}

async function handleSave() {
    saving.value = true
    saveMessage.value = ''
    saveError.value = false
    try {
        await axios.put(API_BASE, {
            min_age: hasAgeRequirement.value ? form.min_age : null,
            guidelines: form.guidelines || null,
            questions: questions.value,
            auto_expire_after: form.auto_expire_after,
            auto_expire_unverified_after: form.auto_expire_unverified_after,
            approval_template: form.approval_template || null,
            rejection_template: form.rejection_template || null,
            admin_email_send_to: form.admin_email_send_to,
            send_rejection_email: form.send_rejection_email
        })
        saveMessage.value = 'Settings saved!'
        setTimeout(() => (saveMessage.value = ''), 3000)
    } catch (e) {
        saveError.value = true
        saveMessage.value = e.response?.data?.message || 'Failed to save settings.'
    } finally {
        saving.value = false
    }
}

function questionTypeLabel(type) {
    return questionTypes.find((t) => t.value === type)?.label || type
}

function resetQuestionForm() {
    questionForm.label = ''
    questionForm.type = 'text'
    questionForm.required = false
    questionForm.options = []
}

function resetReasonForm() {
    reasonForm.title = ''
    reasonForm.note = ''
    reasonForm.reason = ''
}

function startEditQuestion(question) {
    editingQuestion.value = question
    questionForm.label = question.label
    questionForm.type = question.type
    questionForm.required = question.required
    questionForm.options = question.type === 'select' ? [...(question.options || [])] : []
}

function startEditReason(reason) {
    editingReason.value = reason
    reasonForm.title = reason.title
    reasonForm.note = reason.note || ''
    reasonForm.reason = reason.reason
}

function closeQuestionModal() {
    showAddQuestion.value = false
    editingQuestion.value = null
    resetQuestionForm()
}

function closeReasonModal() {
    showAddReason.value = false
    editingReason.value = null
    resetReasonForm()
}

function previewReasonMail() {
    const key = editingReason.value.id
    showAddReason.value = false
    editingReason.value = null
    resetReasonForm()
    window.location.href = `/admin/curated-onboarding-settings/mail/preview/rejected/${key}`
}

function handleEmailReceivedPreview() {
    window.location.href = `/admin/curated-onboarding-settings/mail/preview/received`
}

function handleEmailNotifyAdminsPreview() {
    window.location.href = `/admin/curated-onboarding-settings/mail/preview/notify-admin`
}

async function handleSaveQuestion() {
    questionSaving.value = true
    const payload = {
        label: questionForm.label.trim(),
        type: questionForm.type,
        required: questionForm.required,
        options: questionForm.type === 'select' ? questionForm.options.filter((o) => o.trim()) : []
    }
    try {
        if (editingQuestion.value) {
            const { data } = await axios.put(
                `${API_BASE}/questions/${editingQuestion.value.id}`,
                payload
            )
            questions.value = data.questions
        } else {
            const { data } = await axios.post(`${API_BASE}/questions`, payload)
            questions.value = data.questions
        }
        closeQuestionModal()
    } catch (e) {
        console.error('Save question failed:', e)
    } finally {
        questionSaving.value = false
    }
}

async function handleSaveReason() {
    reasonSaving.value = true
    const payload = {
        title: reasonForm.title.trim(),
        note: reasonForm.note.trim() || null,
        reason: reasonForm.reason.trim()
    }
    try {
        if (editingReason.value) {
            const { data } = await axios.put(
                `${API_BASE}/rejection-reasons/${editingReason.value.id}`,
                payload
            )
            rejectionReasons.value = data.rejection_reasons
        } else {
            const { data } = await axios.post(`${API_BASE}/rejection-reasons`, payload)
            rejectionReasons.value = data.rejection_reasons
        }
        closeReasonModal()
    } catch (e) {
        console.error('Save reason failed:', e)
    } finally {
        reasonSaving.value = false
    }
}

async function handleDeleteQuestion(question) {
    if (!confirm(`Remove the question "${question.label}"?`)) return
    questionLoading.value = question.id
    try {
        const { data } = await axios.delete(`${API_BASE}/questions/${question.id}`)
        questions.value = data.questions
    } catch (e) {
        console.error('Delete question failed:', e)
    } finally {
        questionLoading.value = null
    }
}

async function handleDeleteReason(reason) {
    if (!confirm(`Remove the rejection reason "${reason.title}"?`)) return
    reasonLoading.value = reason.id
    try {
        const { data } = await axios.delete(`${API_BASE}/rejection-reasons/${reason.id}`)
        rejectionReasons.value = data.rejection_reasons
    } catch (e) {
        console.error('Delete reason failed:', e)
    } finally {
        reasonLoading.value = null
    }
}

onMounted(loadSettings)
</script>
