<template>
    <MainLayout>
        <KitLoaderSkeleton v-if="loading" />

        <KitFeatureDisabled v-else-if="isDisabled && !loading" />

        <KitLoaderError v-else-if="error" :error="error" @retry="retryLoad" />

        <div v-else class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body lg:-mt-5">
            <div class="max-w-5xl mx-auto px-6 md:px-12">
                <div class="pt-14 pb-8 flex items-start justify-between">
                    <router-link
                        :to="`/starter-kits/${route.params.id}/${route.params.slug}`"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors group"
                    >
                        <ChevronLeftIcon
                            class="w-4 h-4 transition-transform group-hover:-translate-x-0.5"
                        />
                        {{ t('common.backToKit') }}
                    </router-link>

                    <button
                        @click="saveDetails"
                        :disabled="saving || !detailsDirty"
                        class="self-start sm:self-auto flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex-shrink-0"
                        :class="
                            detailsDirty
                                ? 'bg-primary hover:bg-[#D91B42] text-white shadow-md shadow-primary/25'
                                : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-400 cursor-not-allowed'
                        "
                    >
                        <Spinner v-if="saving" size="sm" />
                        <CheckIcon v-else-if="saveSuccess" class="w-4 h-4" />
                        <ArrowDownTrayIcon v-else class="w-4 h-4" />
                        {{
                            saving
                                ? t('common.saving') + '…'
                                : saveSuccess
                                  ? t('common.saved') + '!'
                                  : t('post.saveChanges')
                        }}
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-10">
                    <div>
                        <div class="flex items-center gap-2.5 mb-4">
                            <span class="block w-8 h-px bg-primary"></span>
                            <span
                                class="text-xs font-semibold tracking-[0.15em] uppercase text-primary"
                                >{{ t('common.editing') }} {{ t('common.starterKit') }}</span
                            >
                        </div>
                        <h1
                            class="font-display font-bold text-gray-950 dark:text-white leading-tight tracking-tight break-all"
                            :class="[kit.title.length > 30 ? 'text-3xl' : 'text-4xl md:text-5xl ']"
                        >
                            {{ kit.title }}
                        </h1>
                    </div>
                </div>

                <div
                    v-if="kit.status !== 10"
                    :class="[
                        'mb-4 rounded-2xl p-4 flex items-start gap-3.5 border text-sm',
                        kit.status === 0
                            ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-300'
                            : 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800/60 text-red-800 dark:text-red-300'
                    ]"
                >
                    <ClockIcon v-if="kit.status === 0" class="w-5 h-5 flex-shrink-0 mt-0.5" />
                    <ExclamationTriangleIcon
                        v-else-if="kit.status === 2"
                        class="w-5 h-5 flex-shrink-0 mt-0.5"
                    />
                    <XCircleIcon v-else class="w-5 h-5 flex-shrink-0 mt-0.5" />
                    <div>
                        <p v-if="kit.status === 0" class="font-semibold mb-0.5">
                            {{ t('common.pendingReview') }}
                        </p>

                        <p v-else-if="kit.status === 2" class="font-semibold mb-0.5">
                            {{ t('common.starterKitDisabled') }}
                        </p>

                        <p v-else-if="kit.status === 5" class="font-semibold mb-0.5">
                            {{ t('common.kitSuspended') }}
                        </p>

                        <p v-if="kit.status === 0" class="text-xs opacity-80 leading-relaxed">
                            {{
                                t(
                                    'common.yourKitIsAwaitingModeratorApprovalBeforeItAppearsPublicly'
                                )
                            }}
                        </p>

                        <p v-else-if="kit.status === 2" class="text-xs opacity-80 leading-relaxed">
                            {{
                                t(
                                    'common.thisKitWasRejectedYouCanUpdateTheDetailsAndResubmitForReview'
                                )
                            }}
                        </p>

                        <p v-else-if="kit.status === 5" class="text-xs opacity-80 leading-relaxed">
                            {{ t('common.thisKitWasSuspendedAndCannotBeUpdated') }}
                        </p>
                    </div>
                </div>

                <Transition name="fade">
                    <div
                        v-if="pendingChange"
                        class="mb-8 rounded-2xl p-4 flex items-start gap-3.5 border text-sm bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800/60 text-blue-800 dark:text-blue-300"
                    >
                        <ClockIcon class="w-5 h-5 flex-shrink-0 mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold mb-0.5">
                                {{ t('common.changesAwaitingReview') }}
                            </p>
                            <p class="text-xs opacity-80 leading-relaxed">
                                {{
                                    t(
                                        'common.someFieldsHaveBeenUpdatedAndAreWaitingForModeratorApproval'
                                    )
                                }}
                                {{
                                    t(
                                        'common.youCanStillMakeNewChangesWhichWillReplaceThePendingOnes'
                                    )
                                }}
                            </p>
                            <div class="flex flex-wrap gap-1.5 mt-2.5">
                                <span
                                    v-for="(data, field) in pendingChange.changes"
                                    :key="field"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold border"
                                    :class="
                                        data.status === 'approved'
                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-400'
                                            : data.status === 'rejected'
                                              ? 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800/60 text-red-600 dark:text-red-400'
                                              : 'bg-blue-100 dark:bg-blue-900/40 border-blue-200 dark:border-blue-700/60 text-blue-700 dark:text-blue-300'
                                    "
                                >
                                    <CheckCircleIcon
                                        v-if="data.status === 'approved'"
                                        class="w-3 h-3"
                                    />
                                    <XCircleIcon
                                        v-else-if="data.status === 'rejected'"
                                        class="w-3 h-3"
                                    />
                                    <ClockIcon v-else class="w-3 h-3" />
                                    {{ fieldLabel(field) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </Transition>

                <div
                    v-if="[0, 2, 10].includes(kit.status)"
                    class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-16"
                >
                    <div class="lg:col-span-2 space-y-8">
                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden"
                        >
                            <button
                                class="w-full px-6 py-4 flex items-center justify-between gap-4 transition-colors cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800/40"
                                :class="
                                    showMedia
                                        ? 'border-b border-gray-100 dark:border-gray-800'
                                        : 'border-gray-100 dark:border-gray-800'
                                "
                                @click="showMedia = !showMedia"
                            >
                                <div class="text-left">
                                    <div class="flex items-center gap-2">
                                        <h2
                                            class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider"
                                        >
                                            {{ t('common.media') }}
                                        </h2>
                                        <PendingFieldBadge v-if="hasPendingMediaFields" />
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ t('common.iconAndHeaderImage') }}
                                    </p>
                                </div>
                                <ChevronUpIcon
                                    class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                    :class="showMedia ? 'rotate-0' : 'rotate-180'"
                                />
                            </button>

                            <Transition
                                enter-active-class="transition-all duration-200 ease-out"
                                enter-from-class="opacity-0 -translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition-all duration-150 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <div v-if="showMedia" class="p-6 space-y-6">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                        >
                                            <span class="flex items-center gap-2">
                                                {{ t('common.headerImage') }}
                                                <span class="text-xs font-normal text-gray-400"
                                                    >1500 × 600 required</span
                                                >
                                                <PendingFieldBadge
                                                    v-if="hasPendingField('header_path')"
                                                />
                                            </span>
                                        </label>

                                        <div
                                            v-if="hasPendingField('header_path')"
                                            class="mb-3 rounded-2xl overflow-hidden border-2 border-blue-300 dark:border-blue-700 relative"
                                        >
                                            <img
                                                v-if="
                                                    pendingChange.changes.header_path.action !==
                                                    'delete'
                                                "
                                                :src="pendingChange.changes.header_path.preview_url"
                                                class="w-full object-cover aspect-[5/3]"
                                                alt="Pending header"
                                            />
                                            <div
                                                v-else
                                                class="aspect-[5/3] flex items-center justify-center bg-red-50 dark:bg-red-950/30"
                                            >
                                                <div
                                                    class="flex flex-col items-center gap-2 text-red-500 dark:text-red-400"
                                                >
                                                    <TrashIcon class="w-6 h-6" />
                                                    <span class="text-xs font-semibold">{{
                                                        t('common.pendingRemoval')
                                                    }}</span>
                                                </div>
                                            </div>
                                            <div class="absolute top-2 left-2">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-bold bg-blue-500 text-white"
                                                >
                                                    <ClockIcon class="w-3 h-3" />
                                                    {{ t('common.pendingReview') }}
                                                </span>
                                            </div>
                                            <div class="absolute top-2 right-2">
                                                <span
                                                    class="text-[11px] text-white/80 bg-black/40 px-2 py-1 rounded-lg"
                                                >
                                                    {{ t('common.current') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div
                                            class="relative group rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 transition-colors hover:border-primary/50"
                                            :class="[
                                                'aspect-[5/3]',
                                                headerUrl ? 'border-solid border-transparent' : ''
                                            ]"
                                            @dragover.prevent
                                            @drop.prevent="onDrop('header', $event)"
                                            @click="!headerUrl && $refs.headerInput.click()"
                                        >
                                            <img
                                                v-if="headerUrl"
                                                :src="headerUrl"
                                                class="w-full h-full object-cover"
                                                alt="Header"
                                            />

                                            <div
                                                v-else
                                                class="absolute inset-0 flex flex-col items-center justify-center gap-2 cursor-pointer"
                                            >
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center"
                                                >
                                                    <PhotoIcon class="w-5 h-5 text-gray-400" />
                                                </div>
                                                <p class="text-xs text-gray-400 font-medium">
                                                    {{ t('common.clickOrDragToUpload') }}
                                                </p>
                                                <p
                                                    class="text-[11px] text-gray-300 dark:text-gray-600"
                                                >
                                                    JPG, PNG, WEBP · max 5MB
                                                </p>
                                            </div>

                                            <div
                                                v-if="uploadingHeader"
                                                class="absolute inset-0 bg-black/50 flex items-center justify-center backdrop-blur-sm"
                                            >
                                                <Spinner size="md" class="text-white" />
                                            </div>

                                            <div
                                                v-if="headerUrl && !uploadingHeader"
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100"
                                            >
                                                <button
                                                    @click.stop="$refs.headerInput.click()"
                                                    class="px-3 py-1.5 bg-white/90 hover:bg-white rounded-xl text-xs font-bold text-gray-800 transition-all flex items-center gap-1.5"
                                                >
                                                    <ArrowUpTrayIcon class="w-3.5 h-3.5" />
                                                    {{ t('common.replace') }}
                                                </button>
                                                <button
                                                    @click.stop="deleteImage('header')"
                                                    :disabled="deletingHeader"
                                                    class="px-3 py-1.5 bg-red-500/90 hover:bg-red-500 rounded-xl text-xs font-bold text-white transition-all flex items-center gap-1.5"
                                                >
                                                    <TrashIcon class="w-3.5 h-3.5" />
                                                    {{ t('common.remove') }}
                                                </button>
                                            </div>

                                            <div
                                                v-if="hasPendingField('header_path')"
                                                class="absolute bottom-2 left-2 right-2"
                                            >
                                                <p
                                                    class="text-[11px] text-center text-white/70 bg-black/30 rounded-lg py-1"
                                                >
                                                    {{ t('common.liveVersion') }}
                                                </p>
                                            </div>
                                        </div>
                                        <input
                                            ref="headerInput"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                            @change="onFileChange('header', $event)"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                        >
                                            <span class="flex items-center gap-2">
                                                {{ t('common.icon') }}
                                                <span class="text-xs font-normal text-gray-400"
                                                    >Square, min 100 × 100</span
                                                >
                                                <PendingFieldBadge
                                                    v-if="hasPendingField('icon_path')"
                                                />
                                            </span>
                                        </label>

                                        <div class="flex items-center gap-4">
                                            <div class="relative flex-shrink-0">
                                                <p
                                                    class="text-[11px] text-gray-400 mb-1 text-center"
                                                >
                                                    {{ t('common.current') }}
                                                </p>
                                                <div
                                                    class="relative group w-20 h-20 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 transition-colors hover:border-primary/50 cursor-pointer"
                                                    :class="
                                                        iconUrl
                                                            ? 'border-solid border-transparent'
                                                            : ''
                                                    "
                                                    @dragover.prevent
                                                    @drop.prevent="onDrop('icon', $event)"
                                                    @click="$refs.iconInput.click()"
                                                >
                                                    <img
                                                        v-if="iconUrl"
                                                        :src="iconUrl"
                                                        class="w-full h-full object-cover"
                                                        alt="Icon"
                                                    />
                                                    <div
                                                        v-else
                                                        class="absolute inset-0 flex flex-col items-center justify-center gap-1"
                                                    >
                                                        <PhotoIcon
                                                            class="w-6 h-6 text-gray-300 dark:text-gray-600"
                                                        />
                                                    </div>
                                                    <div
                                                        v-if="uploadingIcon"
                                                        class="absolute inset-0 bg-black/50 flex items-center justify-center"
                                                    >
                                                        <Spinner size="sm" class="text-white" />
                                                    </div>
                                                    <div
                                                        v-if="iconUrl && !uploadingIcon"
                                                        class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100"
                                                    >
                                                        <ArrowUpTrayIcon
                                                            class="w-4 h-4 text-white"
                                                        />
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="
                                                        hasPendingField('icon_path') &&
                                                        pendingChange.changes.icon_path.preview_url
                                                    "
                                                    class="absolute top-4.5 right-0 w-5 h-5 rounded-full bg-blue-500 border-2 border-white dark:border-gray-900 flex items-center justify-center"
                                                    :title="t('common.pendingReview')"
                                                >
                                                    <ClockIcon class="w-3 h-3 text-white" />
                                                </div>
                                            </div>

                                            <div
                                                v-if="hasPendingField('icon_path')"
                                                class="flex-shrink-0"
                                            >
                                                <p
                                                    class="text-[11px] text-gray-400 mb-1 text-center"
                                                >
                                                    {{ t('common.pending') }}
                                                </p>
                                                <div
                                                    class="relative w-20 h-20 rounded-2xl overflow-hidden border-2 border-blue-300 dark:border-blue-700"
                                                >
                                                    <img
                                                        v-if="
                                                            pendingChange.changes.icon_path
                                                                .action !== 'delete'
                                                        "
                                                        :src="
                                                            pendingChange.changes.icon_path
                                                                .preview_url
                                                        "
                                                        class="w-full h-full object-cover"
                                                        alt="Pending icon"
                                                    />
                                                    <div
                                                        v-else
                                                        class="w-full h-full flex items-center justify-center bg-red-50 dark:bg-red-950/30"
                                                    >
                                                        <TrashIcon class="w-5 h-5 text-red-400" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed"
                                                >
                                                    {{ t('common.squareImageUsedAsKitThumbnail') }}.
                                                </p>
                                                <div class="flex gap-2 mt-2">
                                                    <button
                                                        @click="$refs.iconInput.click()"
                                                        class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-primary hover:text-primary transition-all flex items-center gap-1.5"
                                                    >
                                                        <ArrowUpTrayIcon class="w-3.5 h-3.5" />
                                                        {{
                                                            iconUrl
                                                                ? t('common.replace')
                                                                : t('common.upload')
                                                        }}
                                                    </button>
                                                    <button
                                                        v-if="iconUrl"
                                                        @click="deleteImage('icon')"
                                                        :disabled="deletingIcon"
                                                        class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-red-200 dark:border-red-800 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition-all"
                                                    >
                                                        {{ t('common.remove') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <input
                                            ref="iconInput"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                            @change="onFileChange('icon', $event)"
                                        />
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 space-y-5"
                        >
                            <h2
                                class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider"
                            >
                                {{ t('common.kitDetails') }}
                            </h2>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    <span class="flex items-center gap-2">
                                        {{ t('common.kitName') }}
                                        <span class="text-primary">*</span>
                                        <PendingFieldBadge v-if="hasPendingField('title')" />
                                    </span>
                                </label>
                                <div class="relative">
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        placeholder="e.g. Science & Tech Creators"
                                        :maxlength="50"
                                        @input="markDirty"
                                        class="w-full px-4 py-2.5 bg-[#FAFAFA] dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all"
                                    />
                                </div>
                                <div class="flex items-center justify-between mt-1.5">
                                    <PendingFieldValue
                                        v-if="hasPendingField('title')"
                                        :value="pendingChange.changes.title.value"
                                    />
                                    <span
                                        class="text-[11px] text-gray-300 dark:text-gray-600 tabular-nums ml-auto"
                                        >{{ form.title.length }}/50</span
                                    >
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    <span class="flex items-center gap-2">
                                        {{ t('studio.description') }}
                                        <span class="text-primary">*</span>
                                        <PendingFieldBadge v-if="hasPendingField('description')" />
                                    </span>
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    maxlength="500"
                                    placeholder="What's this kit about? Help people decide if it's right for them."
                                    @input="markDirty"
                                    class="w-full px-4 py-2.5 bg-[#FAFAFA] dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all resize-none"
                                />
                                <div class="flex items-center justify-between mt-1.5">
                                    <PendingFieldValue
                                        v-if="hasPendingField('description')"
                                        :value="pendingChange.changes.description.value"
                                        truncate
                                    />
                                    <span
                                        class="text-[11px] text-gray-300 dark:text-gray-600 tabular-nums ml-auto"
                                        >{{ form.description.length }}/500</span
                                    >
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    <span class="flex items-center gap-2">
                                        {{ t('common.tags') }} <span class="text-primary">*</span>
                                        <PendingFieldBadge v-if="hasPendingField('hashtags')" />
                                    </span>
                                </label>

                                <div
                                    v-if="hasPendingField('hashtags')"
                                    class="mb-3 p-3 rounded-xl border border-blue-200 dark:border-blue-800/60 bg-blue-50 dark:bg-blue-950/20"
                                >
                                    <p
                                        class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 mb-2 flex items-center gap-1"
                                    >
                                        <ClockIcon class="w-3 h-3" />
                                        {{ t('common.pendingTags') }}
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="tag in pendingChange.changes.hashtags.value"
                                            :key="tag"
                                            class="inline-flex items-center px-2.5 py-1 bg-blue-100 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-700/60 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium"
                                        >
                                            #{{ tag }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    v-if="form.hashtags.length > 0"
                                    class="flex flex-wrap gap-1.5 mb-3"
                                >
                                    <span
                                        v-for="tag in form.hashtags"
                                        :key="tag"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-full text-xs font-medium"
                                    >
                                        #{{ tag }}
                                        <button
                                            @click="removeTag(tag)"
                                            class="text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                        >
                                            <XMarkIcon class="w-3 h-3" />
                                        </button>
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <input
                                        v-model="tagInput"
                                        type="text"
                                        maxlength="32"
                                        :placeholder="`${t('common.addATag')}…`"
                                        class="flex-1 px-4 py-2 bg-[#FAFAFA] dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all"
                                        @keydown.enter.prevent="addTag"
                                        @keydown.space.prevent="addTag"
                                    />
                                    <button
                                        @click="addTag"
                                        :disabled="!tagInput.trim() || form.hashtags.length >= 10"
                                        class="px-4 py-2 text-sm font-semibold rounded-xl border transition-all disabled:opacity-40"
                                        :class="
                                            tagInput.trim()
                                                ? 'bg-primary border-primary text-white hover:bg-[#D91B42]'
                                                : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-400'
                                        "
                                    >
                                        {{ t('common.add') }}
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-600 mt-1.5">
                                    {{ t('common.enterOrSpaceToAdd') }} ·
                                    {{ form.hashtags.length }}/10 {{ t('common.tags') }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden"
                        >
                            <div
                                class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800"
                            >
                                <div>
                                    <h2
                                        class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider"
                                    >
                                        {{ t('common.accounts') }}
                                    </h2>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ accounts.length }}/25 {{ t('settings.added') }} ·
                                        {{ approvedCount }} {{ t('common.approved') }}
                                    </p>
                                </div>
                                <button
                                    @click="showAddModal = true"
                                    :disabled="accounts.length >= 25"
                                    class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all disabled:opacity-40"
                                    :class="
                                        accounts.length < 50
                                            ? 'bg-primary text-white hover:bg-[#D91B42] shadow-sm shadow-primary/20'
                                            : 'bg-gray-100 dark:bg-gray-800 text-gray-400'
                                    "
                                >
                                    <PlusIcon class="w-3.5 h-3.5" />
                                    {{ t('common.addAccount') }}
                                </button>
                            </div>

                            <div class="px-6 py-3 border-b border-gray-50 dark:border-gray-800/60">
                                <div
                                    class="flex gap-1 bg-gray-50 dark:bg-gray-800/60 rounded-xl p-1 w-fit"
                                >
                                    <button
                                        v-for="tab in accountTabs"
                                        :key="tab.value"
                                        @click="accountTab = tab.value"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5"
                                        :class="
                                            accountTab === tab.value
                                                ? 'bg-primary text-white shadow-sm'
                                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                                        "
                                    >
                                        {{ tab.label }}
                                        <span
                                            class="px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="
                                                accountTab === tab.value
                                                    ? 'bg-white/20 text-white'
                                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                                            "
                                            >{{ tabCount(tab.value) }}</span
                                        >
                                    </button>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-50 dark:divide-gray-800/60 px-3">
                                <TransitionGroup name="row">
                                    <div
                                        v-for="account in filteredAccounts"
                                        :key="account.id"
                                        class="group flex items-center gap-3.5 py-3 px-3 hover:bg-gray-50/80 dark:hover:bg-gray-800/30 rounded-xl transition-colors -mx-3 my-0.5"
                                    >
                                        <img
                                            :src="account.avatar"
                                            :alt="account.name"
                                            class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-100 dark:ring-gray-800"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <p
                                                    class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight"
                                                >
                                                    {{ account.name }}
                                                </p>
                                                <span
                                                    v-if="[0, 5].includes(account.kit_status)"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 rounded-full text-[10px] font-bold"
                                                >
                                                    <ClockIcon class="w-3 h-3" />
                                                    {{ t('common.pending') }}
                                                </span>
                                                <span
                                                    v-else-if="account.kit_status === 1"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 rounded-full text-[10px] font-bold"
                                                >
                                                    <CheckCircleIcon class="w-3 h-3" />
                                                    {{ t('common.approved') }}
                                                </span>
                                                <span
                                                    v-else-if="account.kit_status === 2"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/60 rounded-full text-[10px] font-bold"
                                                >
                                                    <XCircleIcon class="w-3 h-3" />
                                                    {{ t('common.rejected') }}
                                                </span>
                                            </div>
                                            <p
                                                class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"
                                            >
                                                @{{ account.username }}
                                                <span
                                                    v-if="!account.local"
                                                    class="text-gray-300 dark:text-gray-600 ml-1"
                                                    >(remote)</span
                                                >
                                            </p>
                                        </div>
                                        <div
                                            class="hidden sm:flex items-center gap-3 text-xs text-gray-400 flex-shrink-0"
                                        >
                                            <span class="flex items-center gap-1.5">
                                                <UsersIcon class="w-3.5 h-3.5" />{{
                                                    formatCount(account.follower_count)
                                                }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <VideoCameraIcon class="w-3.5 h-3.5" />{{
                                                    formatCount(account.post_count)
                                                }}
                                            </span>
                                        </div>
                                        <div
                                            class="flex items-center gap-1.5 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
                                        >
                                            <a
                                                :href="account.url"
                                                target="_blank"
                                                class="px-2.5 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 transition-all"
                                            >
                                                {{ t('studio.view') }}
                                            </a>
                                            <button
                                                v-if="account.rejected_at"
                                                disabled
                                                class="px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-red-50 dark:bg-red-950/40 text-red-400 opacity-50 cursor-not-allowed"
                                            >
                                                {{ t('common.rejected') }}
                                            </button>
                                            <button
                                                v-else
                                                @click="confirmRemove(account)"
                                                class="px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 transition-all"
                                            >
                                                {{ t('common.remove') }}
                                            </button>
                                        </div>
                                    </div>
                                </TransitionGroup>

                                <div v-if="filteredAccounts.length === 0" class="py-14 text-center">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3"
                                    >
                                        <UsersIcon
                                            class="w-5 h-5 text-gray-300 dark:text-gray-600"
                                        />
                                    </div>
                                    <p class="text-sm text-gray-400">
                                        <template v-if="accountTab === 0">{{
                                            $t('common.noPendingAccounts')
                                        }}</template>
                                        <template v-else-if="accountTab === 1">{{
                                            t('common.noApprovedAccountsYet')
                                        }}</template>
                                        <template v-else-if="accountTab === 2">{{
                                            t('common.noRejectedAccounts')
                                        }}</template>
                                        <template v-else>{{
                                            t('common.noAccountsAddedYet')
                                        }}</template>
                                    </p>
                                    <button
                                        v-if="accountTab === 'all'"
                                        @click="showAddModal = true"
                                        class="mt-3 text-sm font-semibold text-primary hover:underline"
                                    >
                                        {{ t('common.addYourFirstAccount') }} →
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                        >
                            <h3
                                class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider mb-4"
                            >
                                {{ t('studio.visibility') }}
                            </h3>
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-semibold text-gray-900 dark:text-white leading-tight"
                                    >
                                        {{ t('common.sensitive') }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                                        {{ t('common.containsSensitiveTopicsOrAccounts') }}.
                                    </p>
                                </div>
                                <button
                                    @click="toggleSensitive"
                                    role="switch"
                                    :aria-checked="form.is_sensitive"
                                    :class="[
                                        'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none flex-shrink-0',
                                        form.is_sensitive
                                            ? 'bg-primary'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform',
                                            form.is_sensitive ? 'translate-x-6' : 'translate-x-1'
                                        ]"
                                    />
                                </button>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                        >
                            <h3
                                class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider mb-4"
                            >
                                {{ t('common.stats') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">{{
                                        t('common.totalAccounts')
                                    }}</span>
                                    <span
                                        class="font-bold text-gray-900 dark:text-white tabular-nums"
                                        >{{ accounts.length
                                        }}<span class="text-gray-400 dark:text-gray-600 font-normal"
                                            >/25</span
                                        ></span
                                    >
                                </div>
                                <div
                                    class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden"
                                >
                                    <div
                                        class="bg-primary h-1.5 rounded-full transition-all"
                                        :style="{
                                            width: `${Math.min(100, (accounts.length / 25) * 100)}%`
                                        }"
                                    ></div>
                                </div>
                                <div class="flex items-center justify-between text-sm pt-1">
                                    <span class="text-gray-500 dark:text-gray-400">{{
                                        t('common.approved')
                                    }}</span>
                                    <span
                                        class="font-bold text-emerald-600 dark:text-emerald-400 tabular-nums"
                                        >{{ approvedCount }}</span
                                    >
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">{{
                                        t('common.pending')
                                    }}</span>
                                    <span class="font-bold text-amber-500 tabular-nums">{{
                                        tabCount(0)
                                    }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">{{
                                        t('common.rejected')
                                    }}</span>
                                    <span class="font-bold text-red-500 tabular-nums">{{
                                        tabCount(2)
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <Transition name="fade">
                            <div
                                v-if="detailsDirty && !saveSuccess"
                                class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-2xl p-4 flex items-start gap-3"
                            >
                                <div
                                    class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0 mt-1.5"
                                ></div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-xs font-semibold text-amber-700 dark:text-amber-300"
                                    >
                                        {{ t('common.unsavedChanges') }}
                                    </p>
                                    <p
                                        class="text-xs text-amber-600 dark:text-amber-400 opacity-80 mt-0.5"
                                    >
                                        {{ t('common.doNotForgetToSaveYourDetails') }}.
                                    </p>
                                </div>
                                <button
                                    @click="saveDetails"
                                    :disabled="saving"
                                    class="flex-shrink-0 text-xs font-bold text-amber-700 dark:text-amber-300 hover:underline"
                                >
                                    {{ t('common.saveNow') }}
                                </button>
                            </div>
                        </Transition>

                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                        >
                            <h3
                                class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider mb-4"
                            >
                                {{ t('common.dangerZone') }}
                            </h3>
                            <div
                                class="rounded-xl border border-red-100 dark:border-red-900/40 bg-red-50/50 dark:bg-red-950/20 p-4"
                            >
                                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                    {{ t('common.deleteThisKit') }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-3 leading-relaxed"
                                >
                                    {{
                                        t(
                                            'common.permanentlyRemovesThisKitAndAllDataCannotBeUndone'
                                        )
                                    }}
                                </p>
                                <button
                                    @click="showDeleteModal = true"
                                    class="w-full py-2 text-xs font-bold rounded-xl border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-500 hover:border-red-500 hover:text-white transition-all"
                                >
                                    {{ t('common.deleteKit') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-20"></div>
            <KitDeleteConfirmModal
                v-model="showDeleteModal"
                :title="`${t('common.deleteThisKit')}?`"
                :confirm-word="kit.title"
                :description="`${t('common.andAllItsDataWillBePermanentlyRemoved')}.`"
                :loading="deleting"
                @confirm="deleteKit"
            />
        </div>

        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="showAddModal"
                    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                >
                    <div
                        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                        @click="closeAddModal"
                    ></div>
                    <div
                        class="relative w-full sm:max-w-md bg-white dark:bg-gray-900 rounded-t-3xl sm:rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden"
                    >
                        <div class="sm:hidden flex justify-center pt-3 pb-1">
                            <div class="w-10 h-1 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        </div>
                        <div
                            class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800"
                        >
                            <h3 class="font-display font-bold text-gray-900 dark:text-white">
                                {{ t('common.addAccount') }}
                            </h3>
                            <button
                                @click="closeAddModal"
                                class="p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 transition-colors"
                            >
                                <XMarkIcon class="w-4 h-4" />
                            </button>
                        </div>
                        <div class="p-5">
                            <div class="relative mb-4">
                                <MagnifyingGlassIcon
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search by username or handle…"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all"
                                    @input="searchAccounts"
                                />
                            </div>
                            <div class="min-h-[200px] max-h-72 overflow-y-auto -mx-1 px-1">
                                <div v-if="searchLoading" class="space-y-2 py-2">
                                    <div
                                        v-for="i in 3"
                                        :key="i"
                                        class="flex items-center gap-3 p-2 animate-pulse"
                                    >
                                        <div
                                            class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex-shrink-0"
                                        ></div>
                                        <div class="flex-1 space-y-1.5">
                                            <div
                                                class="h-3.5 w-28 bg-gray-200 dark:bg-gray-700 rounded"
                                            ></div>
                                            <div
                                                class="h-3 w-20 bg-gray-200 dark:bg-gray-700 rounded"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else-if="
                                        searchQuery && !searchLoading && searchResults.length === 0
                                    "
                                    class="flex flex-col items-center justify-center py-12 text-sm text-gray-400"
                                >
                                    <UserIcon class="w-8 h-8 mb-2 opacity-30" />
                                    {{ t('common.noAccountsFoundFor') }} "{{ searchQuery }}"
                                </div>
                                <div
                                    v-else-if="!searchQuery"
                                    class="flex flex-col items-center justify-center py-12 text-sm text-gray-400"
                                >
                                    <MagnifyingGlassIcon class="w-8 h-8 mb-2 opacity-30" />
                                    {{ t('common.typeToSearchAccounts') }}
                                </div>
                                <div v-else class="space-y-1">
                                    <div
                                        v-for="result in searchResults"
                                        :key="result.id"
                                        class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors"
                                    >
                                        <img
                                            :src="result.avatar"
                                            :alt="result.name"
                                            class="w-9 h-9 rounded-full object-cover flex-shrink-0"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight"
                                            >
                                                {{ result.name }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                @{{ result.username }}
                                                <span
                                                    v-if="!result.local"
                                                    class="text-gray-300 dark:text-gray-600 ml-1"
                                                    >(remote)</span
                                                >
                                            </p>
                                        </div>
                                        <button
                                            v-if="isAlreadyAdded(result.id)"
                                            disabled
                                            class="flex-shrink-0 px-3 py-1.5 bg-gray-50 dark:bg-gray-800 text-gray-300 dark:text-gray-600 text-xs font-bold rounded-xl cursor-not-allowed"
                                        >
                                            {{ t('settings.added') }}
                                        </button>
                                        <button
                                            v-else
                                            @click="addAccount(result)"
                                            :disabled="addingId === result.id"
                                            class="flex-shrink-0 px-3 py-1.5 bg-primary hover:bg-[#D91B42] disabled:bg-primary/60 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-1.5"
                                        >
                                            <Spinner v-if="addingId === result.id" size="sm" />
                                            <span v-else>{{ t('common.add') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="removeTarget"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div
                        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                        @click="removeTarget = null"
                    ></div>
                    <div
                        class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 p-6"
                    >
                        <div
                            class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-950/50 flex items-center justify-center mx-auto mb-4"
                        >
                            <TrashIcon class="w-5 h-5 text-red-500" />
                        </div>
                        <h3
                            class="font-display font-bold text-gray-900 dark:text-white text-center mb-2"
                        >
                            {{ t('common.removeAccount') }}
                        </h3>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6 leading-relaxed"
                        >
                            {{ t('common.remove') }}
                            <span class="font-semibold text-gray-700 dark:text-gray-200"
                                >@{{ removeTarget?.username }}</span
                            >
                            {{ t('common.fromThisKit') }}?
                        </p>
                        <div class="flex gap-3">
                            <button
                                @click="removeTarget = null"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-gray-300 transition-all"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                @click="removeAccount"
                                :disabled="removing"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-xl bg-red-500 hover:bg-red-600 disabled:bg-red-400 text-white transition-all flex items-center justify-center gap-2"
                            >
                                <Spinner v-if="removing" size="sm" />
                                {{ t('common.remove') }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue'
import axios from '@/plugins/axios'
import { useHashids } from '@/composables/useHashids'
import { useUtils } from '@/composables/useUtils'
import { useRoute, useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { useI18n } from 'vue-i18n'
import { useAlertModal } from '@/composables/useAlertModal.js'
import { useKitImageUpload } from '@/composables/useKitImageUpload'
import {
    ChevronLeftIcon,
    XMarkIcon,
    PlusIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    TrashIcon,
    UsersIcon,
    VideoCameraIcon,
    MagnifyingGlassIcon,
    UserIcon,
    PhotoIcon,
    ArrowUpTrayIcon,
    ArrowDownTrayIcon,
    ChevronUpIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'
import { CheckIcon } from '@heroicons/vue/24/solid'
import KitLoaderSkeleton from '@/components/StarterKits/KitLoaderSkeleton.vue'
import KitDeleteConfirmModal from '@/components/StarterKits/KitDeleteConfirmModal.vue'

const PendingFieldBadge = {
    template: `
        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[11px] font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700/60">
            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none">
                <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.2"/>
                <path d="M6 3.5v3l1.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
            Pending review
        </span>
    `
}

const PendingFieldValue = {
    props: { value: String, truncate: Boolean },
    template: `
        <p class="text-[11px] text-blue-500 dark:text-blue-400 flex items-start gap-1 max-w-xs">
            <svg class="w-3 h-3 mt-0.5 flex-shrink-0" viewBox="0 0 12 12" fill="none">
                <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.2"/>
                <path d="M6 3.5v3l1.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
            <span :class="truncate ? 'truncate' : ''">Pending: {{ value }}</span>
        </p>
    `
}

const route = useRoute()
const router = useRouter()
const { decodeHashid } = useHashids()
const { formatCount } = useUtils()
const kitId = decodeHashid(route.params.id)
const { alertModal } = useAlertModal()
const { t } = useI18n()
const appConfig = inject('appConfig')

const loading = ref(true)
const isDisabled = ref(false)
const error = ref(false)
const kit = ref(null)
const accounts = ref([])
const showMedia = ref(false)

const form = ref({ title: '', description: '', hashtags: [], is_sensitive: false })
const tagInput = ref('')
const detailsDirty = ref(false)
const saving = ref(false)
const saveSuccess = ref(false)
const pendingChange = ref(null)

const accountTab = ref('all')
const accountTabs = [
    { label: 'All', value: 'all' },
    { label: 'Approved', value: 1 },
    { label: 'Pending', value: 0 },
    { label: 'Rejected', value: 2 }
]

const {
    iconUrl,
    headerUrl,
    uploadingIcon,
    uploadingHeader,
    deletingIcon,
    deletingHeader,
    uploadImage,
    deleteImage
} = useKitImageUpload(kitId)

const showAddModal = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const searchLoading = ref(false)
const addingId = ref(null)
const removeTarget = ref(null)
const removing = ref(false)
const showDeleteModal = ref(false)
const deleting = ref(false)

const FIELD_LABELS = {
    title: 'Title',
    description: 'Description',
    hashtags: 'Tags',
    icon_path: 'Icon',
    header_path: 'Header'
}

function fieldLabel(field) {
    return FIELD_LABELS[field] ?? field
}

function hasPendingField(field) {
    return !!pendingChange.value?.changes?.[field]
}

const hasPendingMediaFields = computed(
    () => hasPendingField('icon_path') || hasPendingField('header_path')
)

const approvedCount = computed(() => accounts.value.filter((a) => a.kit_status === 1).length)

const filteredAccounts = computed(() => {
    if (accountTab.value === 'all') {
        return accounts.value
    }
    return accounts.value.filter((a) => a.kit_status === accountTab.value)
})

const tabCount = (value) => {
    if (value === 'all') return accounts.value.length
    return accounts.value.filter((a) => a.kit_status === value).length
}

const isAlreadyAdded = (id) => accounts.value.some((a) => a.id === id)

const loadKit = async () => {
    if (appConfig.starterKits === false) {
        isDisabled.value = true
        loading.value = false
        return
    }

    try {
        if (!kitId) throw { response: { status: 404 } }
        const api = axios.getAxiosInstance()

        const [detailsRes, accountsRes, pendingRes] = await Promise.all([
            api.get(`/api/v1/starter-kits/details/${kitId}`),
            api.get(`/api/v1/starter-kits/details/${kitId}/accounts`),
            api
                .get(`/api/v1/starter-kits/details/${kitId}/pending-changes`)
                .catch(() => ({ data: null }))
        ])

        kit.value = detailsRes.data.data

        if (!kit.value.is_owner) {
            error.value = 'forbidden'
            loading.value = false
            return
        }

        iconUrl.value = kit.value.icon_url || null
        headerUrl.value = kit.value.header_url || null
        accounts.value = accountsRes.data.data

        pendingChange.value = pendingRes.data?.data ?? null

        form.value = {
            title: kit.value.title,
            description: kit.value.description || '',
            hashtags: [...(kit.value.hashtags || [])],
            is_sensitive: kit.value.is_sensitive
        }
    } catch (e) {
        const status = e?.response?.status
        error.value = status === 404 ? 'not_found' : status === 403 ? 'forbidden' : 'generic'
    } finally {
        loading.value = false
    }
}

const retryLoad = () => {
    loading.value = true
    error.value = false
    loadKit()
}

const markDirty = () => {
    detailsDirty.value = true
}
const toggleSensitive = () => {
    form.value.is_sensitive = !form.value.is_sensitive
    markDirty()
}

const onFileChange = (type, event) => {
    const file = event.target.files?.[0]
    if (file) uploadImage(type, file)
    event.target.value = ''
}

const onDrop = (type, event) => {
    const file = event.dataTransfer.files?.[0]
    if (file && file.type.startsWith('image/')) uploadImage(type, file)
}

const addTag = () => {
    const tag = tagInput.value.trim().replace(/^#/, '')
    if (!tag || form.value.hashtags.length >= 10) return
    if (form.value.hashtags.some((t) => String(t).toLowerCase() === tag.toLowerCase())) return
    form.value.hashtags.push(tag)
    tagInput.value = ''
    markDirty()
}

const removeTag = (tag) => {
    form.value.hashtags = form.value.hashtags.filter((t) => t !== tag)
    markDirty()
}

const saveDetails = async () => {
    if (!detailsDirty.value) return
    saving.value = true
    try {
        const api = axios.getAxiosInstance()
        const res = await api.post(`/api/v1/starter-kits/details/${kitId}`, {
            title: form.value.title,
            description: form.value.description,
            hashtags: form.value.hashtags,
            is_sensitive: form.value.is_sensitive
        })
        kit.value.title = res.data.data.title
        detailsDirty.value = false
        saveSuccess.value = true

        const api2 = axios.getAxiosInstance()
        const pendingRes = await api2
            .get(`/api/v1/starter-kits/details/${kitId}/pending-changes`)
            .catch(() => ({ data: null }))
        pendingChange.value = pendingRes.data?.data ?? null

        router.replace({ path: res.data.data.path + '/edit' })
        setTimeout(() => {
            saveSuccess.value = false
        }, 2500)
    } catch (e) {
        console.error(e)
        await alertModal(
            '⚠️ ' + t('common.somethingWentWrong'),
            e.response?.data?.message || t('common.unexpectedError')
        )
    } finally {
        saving.value = false
    }
}

const searchAccounts = useDebounceFn(async () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = []
        return
    }
    searchLoading.value = true
    try {
        const api = axios.getAxiosInstance()
        const res = await api.post(`/api/v1/starter-kits/edit/search/accounts/${kitId}`, {
            q: searchQuery.value
        })
        searchResults.value = res.data.data
    } catch (e) {
        console.error(e)
    } finally {
        searchLoading.value = false
    }
}, 350)

const addAccount = async (account) => {
    addingId.value = account.id
    try {
        const api = axios.getAxiosInstance()
        const res = await api.post(`/api/v1/starter-kits/details/${kitId}/accounts/add`, {
            profile_id: account.id
        })
        accounts.value.push({
            ...account,
            kit_status: res.data?.account.kit_status,
            approved_at: res.data?.account.approved_at,
            rejected_at: res.data?.account.rejected_at
        })
    } catch (e) {
        console.error(e)
    } finally {
        addingId.value = null
    }
}

const closeAddModal = () => {
    showAddModal.value = false
    searchQuery.value = ''
    searchResults.value = []
}

const confirmRemove = (account) => {
    removeTarget.value = account
}

const removeAccount = async () => {
    if (!removeTarget.value) return
    removing.value = true
    try {
        const api = axios.getAxiosInstance()
        await api.delete(`/api/v1/starter-kits/details/${kitId}/accounts/${removeTarget.value.id}`)
        accounts.value = accounts.value.filter((a) => a.id !== removeTarget.value.id)
        removeTarget.value = null
    } catch (e) {
        console.error(e)
    } finally {
        removing.value = false
    }
}

const deleteKit = async () => {
    deleting.value = true
    try {
        const api = axios.getAxiosInstance()
        await api.delete(`/api/v1/starter-kits/details/${kitId}`)
        router.push('/starter-kits')
    } catch (e) {
        console.error(e)
        deleting.value = false
        showDeleteModal.value = false
    }
}

onMounted(loadKit)
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}

.row-enter-active,
.row-leave-active {
    transition: all 0.2s ease;
}

.row-enter-from,
.row-leave-to {
    opacity: 0;
    transform: translateX(-6px);
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.15s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.2s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.96) translateY(6px);
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
