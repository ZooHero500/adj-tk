<template>
    <div
        v-if="loading"
        class="max-w-8xl mx-auto pb-6 w-full min-h-[90dvh] flex justify-center items-center"
    >
        <Spinner />
    </div>

    <div v-else-if="error" class="max-w-2xl mx-auto p-6">
        <div class="text-center space-y-6">
            <div class="flex justify-center">
                <div
                    class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center"
                >
                    <WalletIcon class="w-10 h-10 text-gray-400" />
                </div>
            </div>

            <div class="space-y-2">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-200">
                    Starter Kit Not Found
                </h1>
                <p class="text-gray-600 dark:text-gray-500 max-w-md mx-auto">
                    The starter kit you're looking for doesn't exist or may have been removed.
                    Please check the URL or search for a different profile.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                <button
                    @click="goBack"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 hover:dark:bg-gray-950 transition-colors duration-200 cursor-pointer"
                >
                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                    Go Back
                </button>
            </div>
        </div>
    </div>

    <div v-else class="max-w-8xl mx-auto pb-6">
        <div class="flex flex-col xl:flex-row gap-3 items-center justify-between pb-5 px-5">
            <div class="2xl:min-w-[465px]">
                <router-link
                    to="/admin/starterkits"
                    class="flex items-center gap-1 font-medium text-gray-400 dark:text-gray-500"
                >
                    <ChevronLeftIcon class="w-5 h-5" />
                    Back to Starter Kits
                </router-link>
            </div>

            <div>
                <span
                    v-if="kit.status === 10"
                    class="inline-flex items-center gap-1.5 px-6 py-2 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-medium rounded-full"
                >
                    <CheckCircleIcon class="w-5 h-5" />
                    Active
                </span>
                <span
                    v-else-if="kit.status === 5"
                    class="inline-flex items-center gap-1.5 px-6 py-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-sm font-medium rounded-full"
                >
                    <XCircleIcon class="w-5 h-5" />
                    Suspended
                </span>
                <span
                    v-else-if="[0, 1, 2].includes(kit.status)"
                    class="inline-flex items-center gap-1.5 px-6 py-2 bg-yellow-100 dark:bg-yellow-800 text-yellow-600 dark:text-yellow-400 text-sm font-medium rounded-full"
                >
                    <EyeSlashIcon class="w-5 h-5" />
                    Pending
                </span>
                <span
                    v-else
                    class="inline-flex items-center gap-1.5 px-6 py-2 bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 text-sm font-medium rounded-full"
                >
                    <ExclamationCircleIcon class="w-5 h-5" />
                    Unknown State
                </span>
            </div>

            <ButtonGroup
                v-if="actionButtons && actionButtons.length"
                :buttons="actionButtons"
                :loading-value="moderatingAction"
                :disabled="moderating"
                @click="handleActionButton"
            />
        </div>

        <div
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl"
            :class="{ blur: moderating, 'opacity-30': moderating }"
        >
            <div
                class="py-2 px-4 lg:px-8 border-b border-gray-100 dark:border-gray-700 flex flex-col xl:flex-row items-center justify-between gap-4"
            >
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        Manage Starter Kit
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <label class="block text-xs font-light text-gray-400 dark:text-gray-300">
                        Created by
                    </label>
                    <div class="flex items-center gap-3">
                        <img
                            :src="kit.creator.avatar"
                            :alt="kit.creator.name"
                            class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                        />

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-0.5 flex-wrap">
                                <p
                                    class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight"
                                >
                                    {{ kit.creator.name }}
                                </p>
                            </div>
                            <router-link :to="`/@${kit.creator.username}`">
                                <p class="text-xs text-gray-400 dark:text-gray-500 leading-tight">
                                    @{{ kit.creator.username }}
                                </p>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button
                        @click="saveDetails"
                        :disabled="saving || !detailsDirty"
                        class="flex items-center gap-1.5 px-4 xl:px-12 py-2 bg-primary hover:bg-[#E91E63] disabled:bg-gray-200 dark:disabled:bg-gray-700 disabled:text-gray-400 dark:disabled:text-gray-500 text-white text-sm font-semibold rounded-lg transition-colors"
                    >
                        <Spinner v-if="saving" size="sm" />
                        <CheckIcon v-else-if="saveSuccess" class="w-4 h-4" />
                        {{ saving ? 'Saving…' : saveSuccess ? 'Saved!' : 'Save Changes' }}
                    </button>
                </div>
            </div>

            <div class="py-4 space-y-5 px-4 lg:px-8">
                <div class="grid xl:grid-cols-2 gap-6">
                    <div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
                            >
                                Title
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="e.g. Science & Tech Creators"
                                class="w-full px-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                                :maxlength="80"
                                @input="markDirty"
                            />
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 text-right">
                                {{ form.title.length }}/80
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
                            >
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                maxlength="500"
                                placeholder="What's this kit about? Help people decide if it's right for them."
                                class="w-full px-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors resize-none"
                                @input="markDirty"
                            />
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 text-right">
                                {{ form.description.length }}/500
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
                            >
                                Tags
                            </label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span
                                    v-for="tag in form.hashtags"
                                    :key="tag"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-medium group"
                                >
                                    #{{ tag }}
                                    <button
                                        @click="removeTag(tag)"
                                        class="text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition-colors ml-0.5"
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
                                    placeholder="Add a tag…"
                                    class="flex-1 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                                    @keydown.enter.prevent="addTag"
                                    @keydown.space.prevent="addTag"
                                />
                                <button
                                    @click="addTag"
                                    :disabled="!tagInput.trim() || form.hashtags.length >= 10"
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors"
                                >
                                    Add
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Press Enter or Space to add · {{ form.hashtags.length }}/10 tags
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-between py-3 px-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800/60"
                        >
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    Discoverable
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Allow this kit to appear in public listings and search.
                                </p>
                            </div>
                            <button
                                @click="toggleDiscoverable"
                                role="switch"
                                :aria-checked="form.is_discoverable"
                                :class="[
                                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50',
                                    form.is_discoverable
                                        ? 'bg-primary'
                                        : 'bg-gray-200 dark:bg-gray-700'
                                ]"
                            >
                                <span
                                    :class="[
                                        'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                        form.is_discoverable ? 'translate-x-6' : 'translate-x-1'
                                    ]"
                                />
                            </button>
                        </div>

                        <div
                            class="flex items-center justify-between py-3 px-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800/60"
                        >
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    Sensitive
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Content contains sensitive material.
                                </p>
                            </div>
                            <button
                                @click="toggleSensitive"
                                role="switch"
                                :aria-checked="form.is_sensitive"
                                :class="[
                                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50',
                                    form.is_sensitive
                                        ? 'bg-primary'
                                        : 'bg-gray-200 dark:bg-gray-700'
                                ]"
                            >
                                <span
                                    :class="[
                                        'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                        form.is_sensitive ? 'translate-x-6' : 'translate-x-1'
                                    ]"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="hasMedia" class="border-t border-gray-100 dark:border-gray-700"></div>

            <div v-if="hasMedia" class="py-4 px-4 lg:px-8">
                <div
                    class="flex flex-col xl:flex-row items-center justify-between"
                    :class="{ 'mb-5': showMedia }"
                >
                    <div class="w-full flex items-center justify-between mb-5 xl:mb-0">
                        <div class="">
                            <h2
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Media
                            </h2>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
                                Header + icon media
                            </p>
                            <span
                                v-if="hasPendingMedia"
                                class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 rounded text-xs font-medium"
                            >
                                <ClockIcon class="w-3 h-3" /> Pending changes
                            </span>
                        </div>

                        <AnimatedButton
                            v-if="!showMedia"
                            @click="showMedia = true"
                            class="font-bold px-8.5"
                        >
                            Show media
                        </AnimatedButton>

                        <div v-if="!showAllAccounts" class="w-40"></div>
                    </div>
                </div>

                <Transition name="collapse">
                    <div v-if="showMedia" class="grid sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Header
                                <span
                                    class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-1"
                                    >1500 × 600</span
                                >
                            </label>

                            <div
                                class="relative rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700"
                                style="aspect-ratio: 1500/600"
                            >
                                <template v-if="pendingHeaderUrl || kit.header_url">
                                    <img
                                        :src="pendingHeaderUrl || kit.header_url"
                                        class="w-full h-full object-cover"
                                        alt="Header"
                                    />
                                    <div
                                        v-if="pendingHeaderUrl"
                                        class="absolute top-2 left-2 flex items-center gap-1 px-2 py-1 bg-amber-400/90 text-white text-xs font-semibold rounded-full backdrop-blur-sm"
                                    >
                                        <ClockIcon class="w-3 h-3" />
                                        {{ $t('common.pendingApproval') }}
                                    </div>
                                    <button
                                        @click="removeMedia('header')"
                                        :disabled="removingMedia === 'header'"
                                        class="absolute top-2 right-2 flex items-center gap-1.5 px-2.5 py-1.5 bg-red-500/90 hover:bg-red-600 disabled:bg-gray-400 text-white text-xs font-semibold rounded-lg backdrop-blur-sm transition-colors"
                                    >
                                        <Spinner v-if="removingMedia === 'header'" size="xs" />
                                        <TrashIcon v-else class="w-3.5 h-3.5" />
                                        Remove
                                    </button>
                                </template>
                                <div
                                    v-else
                                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-gray-400 dark:text-gray-600"
                                >
                                    <PhotoIcon class="w-10 h-10" />
                                    <span class="text-xs">No header set</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Icon
                                <span
                                    class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-1"
                                    >400 × 400</span
                                >
                            </label>

                            <div class="flex items-start gap-4">
                                <div
                                    class="relative flex-shrink-0 w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700"
                                >
                                    <template v-if="pendingIconUrl || kit.icon_url">
                                        <img
                                            :src="pendingIconUrl || kit.icon_url"
                                            class="w-full h-full object-cover"
                                            alt="Icon"
                                        />
                                        <div
                                            v-if="pendingIconUrl"
                                            class="absolute inset-0 ring-2 ring-amber-400 rounded-2xl pointer-events-none"
                                        />
                                    </template>
                                    <div
                                        v-else
                                        class="absolute inset-0 flex items-center justify-center text-gray-400 dark:text-gray-600"
                                    >
                                        <PhotoIcon class="w-8 h-8" />
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 pt-1">
                                    <span
                                        v-if="pendingIconUrl"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 rounded text-xs font-medium w-fit"
                                    >
                                        <ClockIcon class="w-3 h-3" /> Pending approval
                                    </span>
                                    <p
                                        v-if="!pendingIconUrl && !kit.icon_url"
                                        class="text-xs text-gray-400 dark:text-gray-500"
                                    >
                                        No icon set
                                    </p>
                                    <button
                                        v-if="pendingIconUrl || kit.icon_url"
                                        @click="removeMedia('icon')"
                                        :disabled="removingMedia === 'icon'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-950/60 disabled:opacity-50 text-red-600 dark:text-red-400 text-xs font-medium rounded-lg border border-red-200 dark:border-red-800/60 transition-colors w-fit"
                                    >
                                        <Spinner v-if="removingMedia === 'icon'" size="sm" />
                                        <TrashIcon v-else class="w-3.5 h-3.5" />
                                        Remove icon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            <div class="py-4 px-4 lg:px-8">
                <div
                    class="flex flex-col xl:flex-row items-center justify-between"
                    :class="{ 'mb-5': showAllAccounts }"
                >
                    <div class="w-full flex items-center justify-between mb-5 xl:mb-0">
                        <div class="">
                            <h2
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Accounts
                            </h2>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ accounts.length }} added · {{ approvedCount }} approved
                            </p>
                        </div>

                        <AnimatedButton
                            v-if="!showAllAccounts"
                            @click="showAllAccounts = true"
                            class="font-bold"
                        >
                            Show accounts
                        </AnimatedButton>

                        <div v-if="!showAllAccounts" class="w-40"></div>
                    </div>

                    <div
                        v-if="showAllAccounts"
                        class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-gray-900 rounded-lg w-fit"
                    >
                        <button
                            v-for="tab in accountTabs"
                            :key="tab.value"
                            @click="accountTab = tab.value"
                            :class="[
                                'flex items-center px-3 py-1.5 rounded-md text-xs font-medium transition-colors',
                                accountTab === tab.value
                                    ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm'
                                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                            ]"
                        >
                            {{ tab.label }}
                            <span
                                :class="[
                                    'ml-1.5 px-1.5 py-0.5 rounded-full text-xs',
                                    accountTab === tab.value
                                        ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
                                        : 'bg-gray-200 dark:bg-gray-800 text-gray-500'
                                ]"
                                >{{ tabCount(tab.value) }}</span
                            >
                        </button>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800/60">
                    <TransitionGroup name="row" v-if="showAllAccounts">
                        <div
                            v-for="account in filteredAccounts"
                            :key="account.id"
                            class="flex items-center gap-3 py-2.5 -mx-2 px-2 hover:bg-gray-50/80 dark:hover:bg-gray-900/60 rounded-lg transition-colors group"
                        >
                            <img
                                :src="account.avatar"
                                :alt="account.name"
                                class="w-9 h-9 rounded-full object-cover flex-shrink-0"
                            />

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p
                                        class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight"
                                    >
                                        {{ account.name }}
                                    </p>
                                    <span
                                        v-if="[0, 5].includes(account.kit_status)"
                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 rounded text-xs font-medium"
                                    >
                                        <ClockIcon class="w-3 h-3" /> Pending
                                    </span>
                                    <span
                                        v-else-if="account.kit_status === 1"
                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800/60 rounded text-xs font-medium"
                                    >
                                        <CheckCircleIcon class="w-3 h-3" /> Approved
                                    </span>
                                    <span
                                        v-else-if="account.kit_status === 2"
                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800/60 rounded text-xs font-medium"
                                    >
                                        <XCircleIcon class="w-3 h-3" /> Rejected
                                    </span>
                                </div>
                                <p
                                    class="text-xs text-gray-400 dark:text-gray-500 leading-tight mt-0.5"
                                >
                                    @{{ account.username }}
                                    <span
                                        v-if="!account.local"
                                        class="ml-1 text-gray-300 dark:text-gray-600"
                                        >(remote)</span
                                    >
                                </p>
                            </div>

                            <div
                                class="hidden sm:flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 flex-shrink-0"
                            >
                                <span class="flex items-center gap-1">
                                    <UsersIcon class="w-3.5 h-3.5" />
                                    {{ formatCount(account.follower_count) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <VideoCameraIcon class="w-3.5 h-3.5" />
                                    {{ formatCount(account.post_count) }}
                                </span>
                            </div>

                            <div
                                class="flex items-center gap-1.5 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                <a
                                    :href="account.url"
                                    target="_blank"
                                    class="px-2.5 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-lg transition-colors"
                                >
                                    View
                                </a>
                            </div>
                        </div>
                    </TransitionGroup>

                    <div
                        v-if="filteredAccounts.length === 0"
                        class="py-10 text-center text-sm text-gray-400 dark:text-gray-500"
                    >
                        <template v-if="accountTab === 0">No pending accounts.</template>
                        <template v-else-if="accountTab === 1">No approved accounts yet.</template>
                        <template v-else-if="accountTab === 2">No rejected accounts.</template>
                        <template v-else>No accounts added yet.</template>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            <div class="py-4 px-4 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2
                            class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            History
                        </h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            Audit trail of all actions taken on this kit by staff.
                        </p>
                    </div>
                </div>

                <div
                    v-if="historyLoading && historyItems.length === 0"
                    class="flex items-center justify-center py-10"
                >
                    <Spinner />
                </div>

                <div v-else class="relative">
                    <div
                        class="absolute left-[17px] top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700/60"
                    ></div>

                    <div class="space-y-1">
                        <div
                            v-for="event in historyItems"
                            :key="event.id"
                            class="relative flex gap-4 pb-5"
                        >
                            <div
                                :class="[
                                    'relative z-10 flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800',
                                    historyEventStyle(event.type).bg
                                ]"
                            >
                                <component
                                    :is="historyEventStyle(event.type).icon"
                                    :class="['w-4 h-4', historyEventStyle(event.type).color]"
                                />
                            </div>

                            <div class="flex-1 min-w-0 pt-1">
                                <div class="flex items-start justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <img
                                            :src="event.actor.avatar"
                                            :alt="event.actor.name"
                                            class="w-5 h-5 rounded-full object-cover flex-shrink-0"
                                        />
                                        <span
                                            class="text-sm text-gray-900 dark:text-white font-medium"
                                        >
                                            {{ event.actor.name }}
                                        </span>
                                        <span
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                            v-html="historyEventLabel(event)"
                                        >
                                        </span>
                                    </div>
                                    <time
                                        class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0"
                                    >
                                        {{ formatHistoryDate(event.created_at) }}
                                    </time>
                                </div>

                                <div
                                    v-if="event.type === 'starterkit:update' && event.value"
                                    class="mt-2 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden text-xs"
                                >
                                    <div v-for="key in historyChangedKeys(event.value)" :key="key">
                                        <div
                                            class="px-3 py-1.5 bg-red-50 dark:bg-red-950/30 border-b border-gray-200 dark:border-gray-700 last:border-b-0"
                                        >
                                            <span
                                                class="text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wide mr-2"
                                                >{{ key }}</span
                                            >
                                            <span
                                                class="line-through text-red-600 dark:text-red-400"
                                                >{{ formatDiffValue(event.value.old[key]) }}</span
                                            >
                                        </div>
                                        <div class="px-3 py-1.5 bg-green-50 dark:bg-green-950/30">
                                            <span
                                                class="text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wide mr-2"
                                                >{{ key }}</span
                                            >
                                            <span class="text-green-700 dark:text-green-400">{{
                                                formatDiffValue(event.value.new[key])
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="historyNextCursor" class="relative flex gap-4 pb-5">
                            <div class="relative z-10 flex-shrink-0 w-9 h-9"></div>
                            <div class="flex-1 pt-1">
                                <button
                                    @click="loadMoreHistory"
                                    :disabled="historyLoading"
                                    class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 disabled:opacity-50 transition-colors"
                                >
                                    <Spinner v-if="historyLoading" size="sm" />
                                    <ChevronDownIcon v-else class="w-3.5 h-3.5" />
                                    Load more
                                </button>
                            </div>
                        </div>

                        <div v-if="kit" class="relative flex gap-4">
                            <div
                                class="relative z-10 flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800 bg-gray-100 dark:bg-gray-700"
                            >
                                <SparklesIcon class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                            </div>
                            <div class="flex-1 min-w-0 pt-1 pb-2">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Starter kit created
                                    </span>
                                    <time
                                        class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0"
                                    >
                                        {{ formatHistoryDate(kit.created_at) }}
                                    </time>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="showDeleteModal"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    @click.self="showDeleteModal = false"
                >
                    <div
                        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                        @click="showDeleteModal = false"
                    ></div>
                    <div
                        class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 p-6"
                    >
                        <div
                            class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-950/40 flex items-center justify-center mx-auto mb-4"
                        >
                            <ExclamationCircleIcon class="w-6 h-6 text-red-500 dark:text-red-400" />
                        </div>
                        <h3
                            class="text-base font-bold text-gray-900 dark:text-white mb-1 text-center"
                        >
                            Delete this kit?
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 text-center">
                            This will permanently delete
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{
                                kit.title
                            }}</span>
                            and all its data. This action cannot be undone.
                        </p>
                        <div class="flex gap-3">
                            <button
                                @click="showDeleteModal = false"
                                class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="deleteKit"
                                :disabled="deleting"
                                class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 disabled:bg-gray-200 dark:disabled:bg-gray-800 disabled:text-gray-400 dark:disabled:text-gray-600 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-1.5"
                            >
                                <Spinner v-if="deleting" size="sm" />
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { useHashids } from '@/composables/useHashids'
import { useUtils } from '@/composables/useUtils'
import { useRoute, useRouter } from 'vue-router'
import { ref, computed, onMounted, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { useI18n } from 'vue-i18n'
import { useAlertModal } from '@/composables/useAlertModal.js'
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
    WalletIcon,
    ExclamationCircleIcon,
    EyeSlashIcon,
    LinkIcon,
    ShieldExclamationIcon,
    PencilSquareIcon,
    ChevronDownIcon,
    SparklesIcon,
    PhotoIcon,
    ArrowTopRightOnSquareIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline'
import { CheckIcon } from '@heroicons/vue/24/solid'
import { starterKitsApi } from '@/services/adminApi'
import AnimatedButton from '@/components/AnimatedButton.vue'
import { useAdminStore } from '@/stores/admin'

const route = useRoute()
const router = useRouter()
const { decodeHashid } = useHashids()
const { formatCount } = useUtils()
const kitId = computed(() => route.params.id)
const { alertModal, confirmModal } = useAlertModal()
const { t } = useI18n()

const loading = ref(true)
const error = ref(false)
const kit = ref(null)
const accounts = ref([])
const adminStore = useAdminStore()

const form = ref({
    title: '',
    description: '',
    hashtags: [],
    is_discoverable: true,
    is_sensitive: false
})
const tagInput = ref('')
const detailsDirty = ref(false)
const saving = ref(false)
const saveSuccess = ref(false)
const moderating = ref(false)
const moderatingAction = ref(null)
const showMedia = ref(false)
const removingMedia = ref(null)

const accountTab = ref('all')
const accountTabs = [
    { label: 'All', value: 'all' },
    { label: 'Approved', value: 1 },
    { label: 'Pending', value: 0 },
    { label: 'Rejected', value: 2 }
]

const showAddModal = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const searchLoading = ref(false)
const addingId = ref(null)

const removeTarget = ref(null)
const removing = ref(false)

const showDeleteModal = ref(false)
const deleting = ref(false)
const history = ref(false)

const historyItems = ref([])
const historyNextCursor = ref(null)
const historyLoading = ref(false)
const showAllAccounts = ref(false)

const HISTORY_IGNORED_DIFF_KEYS = ['updated_at', 'slug']

const historyChangedKeys = (value) => {
    if (!value?.old || !value?.new) return []
    return Object.keys(value.new).filter(
        (k) => !HISTORY_IGNORED_DIFF_KEYS.includes(k) && value.old[k] !== value.new[k]
    )
}

const formatDiffValue = (val) => {
    if (Array.isArray(val)) {
        return val.length ? val.map((t) => `#${t}`).join(', ') : '(none)'
    }
    return val ?? '(empty)'
}

const approvedCount = computed(() => accounts.value.filter((a) => a.kit_status === 1).length)
const pendingHeaderUrl = computed(() => kit.value?.pending_media?.header_path ?? null)
const pendingIconUrl = computed(() => kit.value?.pending_media?.icon_path ?? null)
const hasPendingMedia = computed(() => !!(pendingHeaderUrl.value || pendingIconUrl.value))
const hasMedia = computed(() => {
    if (!kit.value) return false
    return !!(
        kit.value.header_url ||
        kit.value.icon_url ||
        pendingHeaderUrl.value ||
        pendingIconUrl.value
    )
})

const filteredAccounts = computed(() => {
    if (accountTab.value === 'all') return accounts.value
    return accounts.value.filter((a) => a.kit_status === accountTab.value)
})

const tabCount = (value) => {
    if (value === 'all') return accounts.value.length
    return accounts.value.filter((a) => a.kit_status === value).length
}

const isAlreadyAdded = (id) => accounts.value.some((a) => a.id === id)

const loadKit = async () => {
    try {
        const response = await starterKitsApi.getStarterKit(kitId.value)
        kit.value = response.data
        accounts.value = response.data?.accounts

        form.value = {
            title: kit.value.title,
            description: kit.value.description || '',
            hashtags: [...(kit.value.hashtags || [])],
            is_discoverable: kit.value.is_discoverable,
            is_sensitive: kit.value.is_sensitive
        }

        await loadHistory()
    } catch (e) {
        const status = e?.response?.status
        error.value = status === 404 ? 'not_found' : status === 403 ? 'forbidden' : 'generic'
    } finally {
        loading.value = false
    }
}

const actionButtons = computed(() => {
    if (kit.value.status === 0) {
        return [
            { label: 'Approve', value: 'approve', theme: 'success', icon: CheckCircleIcon },
            { label: 'Suspend', value: 'suspend', theme: 'warning', icon: EyeSlashIcon },
            { label: 'View Kit', value: 'viewKit', icon: ArrowTopRightOnSquareIcon },
            { label: 'Delete', value: 'delete', theme: 'danger', icon: TrashIcon }
        ]
    }

    if ([1, 2].includes(kit.value.status)) {
        return [
            { label: 'Approve', value: 'approve', theme: 'success', icon: CheckCircleIcon },
            { label: 'Suspend', value: 'suspend', theme: 'warning', icon: EyeSlashIcon },
            { label: 'View Kit', value: 'viewKit', icon: ArrowTopRightOnSquareIcon },
            { label: 'Delete', value: 'delete', theme: 'danger', icon: TrashIcon }
        ]
    }

    if (kit.value.status === 5) {
        return [
            { label: 'Approve', value: 'approve', theme: 'success', icon: CheckCircleIcon },
            { label: 'Disable', value: 'pending', theme: 'default', icon: ShieldExclamationIcon },
            { label: 'View Kit', value: 'viewKit', icon: ArrowTopRightOnSquareIcon },
            { label: 'Delete', value: 'delete', theme: 'danger', icon: TrashIcon }
        ]
    }

    if (kit.value.status === 10) {
        return [
            { label: 'View Kit', value: 'viewKit', icon: ArrowTopRightOnSquareIcon },
            { label: 'Disable', value: 'pending', theme: 'default', icon: ShieldExclamationIcon },
            { label: 'Suspend', value: 'suspend', theme: 'warning', icon: EyeSlashIcon },
            { label: 'Delete', value: 'delete', theme: 'danger', icon: TrashIcon }
        ]
    }

    return [{ label: 'View Kit', value: 'viewKit', icon: ArrowTopRightOnSquareIcon }]
})

const retryLoad = () => {
    loading.value = true
    error.value = false
    loadKit()
}

const markDirty = () => {
    detailsDirty.value = true
}

const toggleDiscoverable = () => {
    form.value.is_discoverable = !form.value.is_discoverable
    markDirty()
}

const toggleSensitive = () => {
    form.value.is_sensitive = !form.value.is_sensitive
    markDirty()
}

const pollUntilStatusChanges = async (
    previousStatus,
    { maxAttempts = 10, intervalMs = 1500 } = {}
) => {
    for (let i = 0; i < maxAttempts; i++) {
        await new Promise((resolve) => setTimeout(resolve, intervalMs))
        try {
            const response = await starterKitsApi.getStarterKit(kitId.value)
            const fresh = response.data
            if (fresh.status !== previousStatus) {
                kit.value = fresh
                accounts.value = fresh?.accounts ?? accounts.value
                return true
            }
        } catch (e) {
            console.error('Poll error:', e)
        }
    }
    await loadKit()
    return false
}

const handleActionButton = async (val) => {
    if (val.value === 'viewKit') {
        router.push(kit.value.path)
        return
    }

    if (val.value === 'delete') {
        showDeleteModal.value = true
        return
    }

    const actionMap = {
        approve: 'approve',
        suspend: 'suspend',
        pending: 'pending'
    }

    const action = actionMap[val.value]
    if (!action) return

    const previousStatus = kit.value.status
    moderating.value = true
    moderatingAction.value = val.value

    try {
        await starterKitsApi.moderateStarterKit(kitId.value, { action })
        await pollUntilStatusChanges(previousStatus)
        await loadHistory()
    } catch (e) {
        console.error(e)
        await alertModal(
            '⚠️ ' + t('common.somethingWentWrong'),
            e?.response?.data?.message || t('common.unexpectedError')
        )
    } finally {
        moderating.value = false
        moderatingAction.value = null
        await adminStore.fetchReportsCount()
    }
}

const addTag = () => {
    const tag = tagInput.value.trim().replace(/^#/, '')
    if (!tag || form.value.hashtags.length >= 12) {
        return
    }

    const tagLower = tag.toLowerCase()
    const exists = form.value.hashtags.some((t) => String(t).toLowerCase() === tagLower)
    if (exists) {
        return
    }

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
        const res = await starterKitsApi.updateStarterKit(kitId.value, {
            title: form.value.title,
            description: form.value.description,
            hashtags: form.value.hashtags,
            is_discoverable: form.value.is_discoverable,
            is_sensitive: form.value.is_sensitive
        })
        kit.value = res.data
        detailsDirty.value = false
        saveSuccess.value = true
        setTimeout(() => {
            saveSuccess.value = false
        }, 2500)
    } catch (e) {
        console.error(e)
        await alertModal(
            '⚠️ ' + t('common.somethingWentWrong'),
            e.response.data.message || t('common.unexpectedError')
        )
    } finally {
        saving.value = false
        await adminStore.fetchReportsCount()
    }
}

const removeMedia = async (type) => {
    const confirmed = await confirmModal(
        `Remove ${type}?`,
        `This will permanently delete the current ${type} image. This cannot be undone.`
    )
    if (!confirmed) return

    removingMedia.value = type
    try {
        const res = await starterKitsApi.moderateStarterKitMedia(kitId.value, {
            type: type
        })

        kit.value = res.data
    } catch (e) {
        console.error(e)
        await alertModal(
            '⚠️ ' + t('common.somethingWentWrong'),
            e?.response?.data?.message || t('common.unexpectedError')
        )
    } finally {
        removingMedia.value = null
    }
}

const deleteKit = async () => {
    deleting.value = true
    try {
        const res = await starterKitsApi.moderateStarterKit(kitId.value, { action: 'delete' })
        router.push('/admin/starterkits')
    } catch (e) {
        console.error(e)
        deleting.value = false
        showDeleteModal.value = false
    }
}

const historyEventStyle = (type) => {
    const styles = {
        'starterkit:approved': {
            icon: CheckCircleIcon,
            bg: 'bg-green-100 dark:bg-green-900/40',
            color: 'text-green-600 dark:text-green-400'
        },
        'starterkit:field_update_approve': {
            icon: CheckCircleIcon,
            bg: 'bg-green-100 dark:bg-green-900/40',
            color: 'text-green-600 dark:text-green-400'
        },
        'starterkit:suspend': {
            icon: EyeSlashIcon,
            bg: 'bg-red-100 dark:bg-red-900/40',
            color: 'text-red-500 dark:text-red-400'
        },
        'starterkit:field_update_reject': {
            icon: EyeSlashIcon,
            bg: 'bg-red-100 dark:bg-red-900/40',
            color: 'text-red-500 dark:text-red-400'
        },
        'starterkit:pending': {
            icon: ClockIcon,
            bg: 'bg-amber-100 dark:bg-amber-900/40',
            color: 'text-amber-600 dark:text-amber-400'
        },
        'starterkit:update': {
            icon: PencilSquareIcon,
            bg: 'bg-blue-100 dark:bg-blue-900/40',
            color: 'text-blue-600 dark:text-blue-400'
        }
    }
    return (
        styles[type] ?? {
            icon: ExclamationCircleIcon,
            bg: 'bg-gray-100 dark:bg-gray-800',
            color: 'text-gray-400'
        }
    )
}

const historyEventLabel = (event) => {
    const type = event.type
    const labels = {
        'starterkit:approved': 'approved this kit',
        'starterkit:suspend': 'suspended this kit',
        'starterkit:pending': 'set kit to pending',
        'starterkit:update': 'updated kit details',
        'starterkit:field_update_approve': `approved an update to the <strong>${event.value}</strong> field`,
        'starterkit:field_update_reject': `rejected an update to the <strong>${event.value}</strong> field`
    }
    return labels[type] ?? type
}

const formatHistoryDate = (iso) => {
    if (!iso) return ''
    return new Date(iso).toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const loadHistory = async () => {
    historyLoading.value = true
    try {
        const res = await starterKitsApi.getStarterKitHistory(kitId.value)
        historyItems.value = res.data
        historyNextCursor.value = res.meta?.next_cursor ?? null
    } catch (e) {
        console.error(e)
    } finally {
        historyLoading.value = false
    }
}

const loadMoreHistory = async () => {
    if (!historyNextCursor.value || historyLoading.value) return
    historyLoading.value = true
    try {
        const res = await starterKitsApi.getStarterKitHistory(kitId.value, {
            cursor: historyNextCursor.value
        })
        historyItems.value.push(...res.data)
        historyNextCursor.value = res.meta?.next_cursor ?? null
    } catch (e) {
        console.error(e)
    } finally {
        historyLoading.value = false
    }
}

onMounted(loadKit)

watch(
    () => route.params.id,
    (newId) => {
        if (newId) {
            loadKit()
        }
    }
)
</script>

<style scoped>
.row-enter-active,
.row-leave-active {
    transition: all 0.2s ease;
}

.row-enter-from,
.row-leave-to {
    opacity: 0;
    transform: translateX(-8px);
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
    transform: scale(0.96) translateY(8px);
}
</style>
