<template>
    <MainLayout>
        <KitLoaderSkeleton v-if="loading" />
        <KitFeatureDisabled v-if="isDisabled && !loading" />

        <div
            v-else-if="!loading"
            class="min-h-screen bg-[#FAFAFA] dark:bg-neutral-950 font-body lg:-mt-5"
        >
            <div class="max-w-5xl mx-auto px-6 md:px-12">
                <div class="pt-14 pb-8">
                    <router-link
                        to="/starter-kits"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors group"
                    >
                        <ChevronLeftIcon
                            class="w-4 h-4 transition-transform group-hover:-translate-x-0.5"
                        />
                        {{ t('common.starterKits') }}
                    </router-link>
                </div>

                <Transition name="fade">
                    <div
                        v-if="!canCreate"
                        class="flex flex-col items-center justify-center min-h-[80vh] py-20 text-center px-6"
                    >
                        <div class="relative mb-6">
                            <div
                                class="w-20 h-20 rounded-3xl flex items-center justify-center bg-primary/8 dark:bg-primary/12"
                            >
                                <ExclamationTriangleIcon class="w-9 h-9 text-primary/40" />
                            </div>
                        </div>

                        <template v-if="config?.limit_restriction">
                            <h2
                                class="font-display text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2"
                            >
                                {{ t('common.starterKitsUnavailable') }}
                            </h2>
                            <p
                                class="text-gray-500 dark:text-gray-400 text-xl max-w-xl leading-relaxed mb-2"
                            >
                                {{ t('common.yourAccountHasBeen') }}
                                <span class="font-semibold text-gray-700 dark:text-gray-200">
                                    {{ t('common.restricted') }}
                                </span>
                                {{ t('common.fromThisFeatureByTheAdminTeam') }}.
                            </p>
                            <p
                                class="text-lg text-gray-400 dark:text-gray-500 max-w-xl leading-relaxed mb-8"
                            >
                                {{ t('common.pleaseContactThemForAssistance') }}.
                            </p>
                        </template>

                        <template v-else-if="config?.min_followers_not_met">
                            <h2
                                class="font-display text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2"
                            >
                                {{ t('common.followersRequirementNotMet') }}
                            </h2>
                            <p
                                class="text-gray-500 dark:text-gray-400 text-base max-w-xl leading-relaxed mb-1"
                            >
                                {{ t('common.creatingAStarterKitRequiresAMinimumOf') }}
                                <span class="font-semibold text-gray-700 dark:text-gray-200">
                                    {{ config.min_followers_required.toLocaleString() }}
                                    {{ t('common.followers') }}.
                                </span>
                            </p>
                            <p
                                class="text-sm text-gray-400 dark:text-gray-500 max-w-xl leading-relaxed mb-8"
                            >
                                {{
                                    t(
                                        'common.keepGrowingYourAudienceAndCheckBackOnceYouHaveHitTheThreshold'
                                    )
                                }}.
                            </p>

                            <div
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-full shadow-sm mb-8"
                            >
                                <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
                                <span
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 tabular-nums"
                                >
                                    {{ config.min_followers_required.toLocaleString() }}
                                    {{ t('common.followersRequired') }}
                                </span>
                            </div>
                        </template>

                        <template v-else>
                            <h2
                                class="font-display text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2"
                            >
                                {{ t('common.starterKitLimitReached') }}
                            </h2>
                            <p
                                class="text-gray-500 dark:text-gray-400 text-base max-w-xl leading-relaxed mb-1"
                            >
                                {{ t('common.youveCreatedTheMaximumOf') }}
                                <span class="font-semibold text-gray-700 dark:text-gray-200">
                                    {{ config?.max_kits ?? '—' }} Starter
                                    {{ config?.max_kits === 1 ? 'Kit' : 'Kits' }}
                                </span>
                                {{ t('common.allowedPerAccount') }}.
                            </p>
                            <p
                                class="text-sm text-gray-400 dark:text-gray-500 max-w-xl leading-relaxed mb-8"
                            >
                                {{ t('common.deleteAnExistingStarterKittoFreeUpASlot') }}
                            </p>

                            <div
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-full shadow-sm mb-8"
                            >
                                <span
                                    class="w-2 h-2 rounded-full bg-primary flex-shrink-0"
                                ></span>
                                <span
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 tabular-nums"
                                >
                                    {{ config?.total_kits ?? '—' }} / {{ config?.max_kits ?? '—' }}
                                    {{ t('common.kitsUsed') }}
                                </span>
                            </div>
                        </template>

                        <router-link
                            v-if="config?.limit_restriction"
                            to="/"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold shadow-lg transition-all bg-primary hover:bg-[#D91B42] text-white shadow-primary/25"
                        >
                            <ChevronLeftIcon class="w-4 h-4" />
                            {{ t('common.goBackHome') }}
                        </router-link>

                        <router-link
                            v-else-if="config?.min_followers_not_met"
                            to="/starter-kits"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold shadow-lg transition-all bg-primary hover:bg-[#D91B42] text-white shadow-primary/25"
                        >
                            <ChevronLeftIcon class="w-4 h-4" />
                            {{
                                config?.min_followers_not_met
                                    ? t('common.backToStarterKits')
                                    : t('common.manageMyKits')
                            }}
                        </router-link>

                        <router-link
                            v-else-if="config?.total_kits === config?.max_kits"
                            to="/starter-kits/my-kits"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold shadow-lg transition-all bg-primary hover:bg-[#D91B42] text-white shadow-primary/25"
                        >
                            <ChevronLeftIcon class="w-4 h-4" />
                            {{ t('common.manageMyKits') }}
                        </router-link>

                        <p
                            v-if="!config?.limit_restriction && !isDisabled"
                            class="mt-5 text-xs text-gray-400 dark:text-gray-600"
                        >
                            {{
                                config?.min_followers_not_met
                                    ? t(
                                          'common.thisRequirementHelpsEnsureQualityStarterKitsForTheCommunity'
                                      )
                                    : t('common.deleteAKitFromYourDashboardToCreateANewOne')
                            }}
                        </p>
                    </div>
                </Transition>

                <div v-if="canCreate">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-5">
                        <div>
                            <div class="flex items-center gap-2.5 mb-4">
                                <span class="block w-8 h-px bg-primary"></span>
                                <span
                                    class="text-xs font-semibold tracking-[0.15em] uppercase text-primary"
                                >
                                    {{ t('common.newStarterKit') }}
                                </span>
                            </div>
                            <h1
                                class="font-display text-4xl md:text-5xl font-bold text-gray-950 dark:text-white leading-tight tracking-tight"
                            >
                                {{ t('common.createAStarterKit') }}
                            </h1>
                            <p class="text-gray-500 dark:text-gray-400 mt-2 text-base">
                                {{ t('common.curateACollectionOfAccountsWorthFollowing') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-20">
                        <div class="lg:col-span-2 space-y-6">
                            <div
                                class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 space-y-2"
                            >
                                <h2
                                    class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider mb-5"
                                >
                                    {{ t('common.starterKitDetails') }}
                                </h2>

                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        {{ t('common.kitName') }}
                                        <span class="text-primary">*</span>
                                    </label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="e.g. Creative Content Creators"
                                        :maxlength="80"
                                        class="w-full px-4 py-2.5 bg-[#FAFAFA] dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all"
                                        :class="{ 'border-primary': form.name.length > 0 }"
                                    />
                                    <div class="flex justify-end mt-1.5">
                                        <span
                                            class="text-[11px] text-gray-300 dark:text-gray-600 tabular-nums"
                                            >{{ form.name.length }}/80</span
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        {{ t('studio.description') }}
                                        <span class="text-primary">*</span>
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        rows="4"
                                        maxlength="500"
                                        :placeholder="`${t('common.describeWhatThisStarterKitIsAboutAndWhoItsFor')}…`"
                                        class="w-full px-4 py-2.5 bg-[#FAFAFA] dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all resize-none"
                                    />
                                    <div class="flex justify-end mt-1.5">
                                        <span
                                            class="text-[11px] text-gray-300 dark:text-gray-600 tabular-nums"
                                            >{{ form.description.length }}/500</span
                                        >
                                    </div>
                                </div>

                                <div ref="hashtagContainerRef">
                                    <label
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        {{ t('common.tags') }} <span class="text-primary">*</span>
                                        <span class="ml-1.5 text-xs font-normal text-gray-400"
                                            >{{ t('common.upTo') }} 10</span
                                        >
                                    </label>

                                    <div
                                        v-if="form.hashtags.length > 0"
                                        class="flex flex-wrap gap-1.5 mb-3"
                                    >
                                        <span
                                            v-for="(tag, idx) in form.hashtags"
                                            :key="idx"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-primary/10 dark:bg-primary/15 text-primary border border-primary/20 rounded-full text-xs font-semibold"
                                        >
                                            #{{ tag }}
                                            <button
                                                @click="removeHashtag(idx)"
                                                class="hover:bg-primary/20 rounded-full p-0.5 transition-colors"
                                            >
                                                <XMarkIcon class="w-3 h-3" />
                                            </button>
                                        </span>
                                    </div>

                                    <div class="relative">
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <span
                                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium pointer-events-none"
                                                    >#</span
                                                >
                                                <input
                                                    ref="hashtagInputRef"
                                                    v-model="hashtagInput"
                                                    type="text"
                                                    :maxlength="32"
                                                    autocomplete="off"
                                                    :disabled="form.hashtags.length >= 10"
                                                    :placeholder="
                                                        form.hashtags.length >= 10
                                                            ? t('common.limitReached')
                                                            : t('common.addATag') + '…'
                                                    "
                                                    class="w-full pl-7 pr-4 py-2.5 bg-[#FAFAFA] dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all disabled:opacity-40"
                                                    @keydown.enter.prevent="addHashtag"
                                                    @keydown.down.prevent="
                                                        navigateHashtagDropdown(1)
                                                    "
                                                    @keydown.up.prevent="
                                                        navigateHashtagDropdown(-1)
                                                    "
                                                    @keydown.escape="closeHashtagDropdown"
                                                    @focus="onHashtagFocus"
                                                    @input="onHashtagInput"
                                                />
                                            </div>
                                            <button
                                                @click="addHashtag"
                                                :disabled="
                                                    !hashtagInput.trim() ||
                                                    form.hashtags.length >= 10
                                                "
                                                class="px-4 py-2 text-sm font-bold rounded-xl border transition-all disabled:opacity-40"
                                                :class="
                                                    hashtagInput.trim()
                                                        ? 'bg-primary border-primary text-white hover:bg-[#D91B42]'
                                                        : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-400'
                                                "
                                            >
                                                {{ t('common.add') }}
                                            </button>
                                        </div>

                                        <Transition name="dropdown">
                                            <div
                                                v-if="
                                                    showHashtagDropdown &&
                                                    hashtagSuggestions.length > 0
                                                "
                                                class="absolute z-20 top-full left-0 right-14 mt-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-xl overflow-hidden"
                                            >
                                                <p
                                                    class="px-3 pt-2.5 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider"
                                                >
                                                    {{ t('common.suggestions') }}
                                                </p>
                                                <button
                                                    v-for="(suggestion, idx) in hashtagSuggestions"
                                                    :key="suggestion.tag"
                                                    @mousedown.prevent="
                                                        selectHashtagSuggestion(suggestion)
                                                    "
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-left transition-colors"
                                                    :class="
                                                        hashtagDropdownIndex === idx
                                                            ? 'bg-primary/10 text-primary'
                                                            : 'hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200'
                                                    "
                                                >
                                                    <span
                                                        class="w-6 h-6 flex items-center justify-center bg-primary/10 text-primary rounded-lg text-xs font-bold flex-shrink-0"
                                                        >#</span
                                                    >
                                                    <span class="text-sm font-medium">{{
                                                        suggestion.name
                                                    }}</span>
                                                </button>
                                            </div>
                                        </Transition>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1.5">
                                        {{ t('common.firstTagSetsTheMainCategory') }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl"
                            >
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between"
                                >
                                    <div>
                                        <h2
                                            class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider"
                                        >
                                            {{ t('common.accounts') }}
                                        </h2>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ form.selectedAccounts.length }}
                                            {{ t('common.added') }} · {{ t('common.upTo') }}
                                            {{ MAX_ACCOUNTS }}
                                        </p>
                                    </div>
                                    <button
                                        v-if="form.selectedAccounts.length > 0"
                                        @click="clearSelectedAccounts"
                                        class="text-xs font-semibold text-red-500 hover:text-red-600 transition-colors"
                                    >
                                        {{ t('nav.clearAll') }}
                                    </button>
                                </div>

                                <div class="p-5">
                                    <div class="relative mb-4" ref="accountSearchContainerRef">
                                        <MagnifyingGlassIcon
                                            v-if="!isSearching && !accountLimitReached"
                                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                        />
                                        <div v-else-if="!isSearching && accountLimitReached"></div>
                                        <Spinner
                                            v-else
                                            size="xs"
                                            class="absolute left-3.5 top-1/2 -translate-y-1/2"
                                        />
                                        <input
                                            ref="accountSearchInputRef"
                                            v-if="!accountLimitReached"
                                            v-model="searchQuery"
                                            type="text"
                                            :disabled="accountLimitReached"
                                            :placeholder="`${t('common.searchByUsernameOrDisplayName')}…`"
                                            autocomplete="off"
                                            class="w-full pl-10 pr-9 py-2.5 bg-[#FAFAFA] dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all"
                                            @input="onAccountSearchInput"
                                            @keydown.down.prevent="navigateAccountDropdown(1)"
                                            @keydown.up.prevent="navigateAccountDropdown(-1)"
                                            @keydown.enter.prevent="selectHighlightedAccount"
                                            @keydown.escape="closeAccountDropdown"
                                            @focus="onAccountSearchFocus"
                                        />
                                        <button
                                            v-if="searchQuery"
                                            @click="clearSearch"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                        >
                                            <XMarkIcon class="w-3.5 h-3.5 text-gray-400" />
                                        </button>

                                        <Transition name="dropdown">
                                            <div
                                                v-if="showAccountDropdown"
                                                class="absolute z-20 top-full left-0 right-0 mt-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-xl overflow-scroll max-h-[300px]"
                                            >
                                                <div v-if="isSearching" class="p-2 space-y-1">
                                                    <div
                                                        v-for="i in 3"
                                                        :key="i"
                                                        class="flex items-center gap-3 px-3 py-2.5 animate-pulse"
                                                    >
                                                        <div
                                                            class="w-9 h-9 bg-gray-200 dark:bg-gray-700 rounded-full flex-shrink-0"
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

                                                <div v-else-if="searchResults.length > 0">
                                                    <p
                                                        class="px-3 pt-2.5 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider"
                                                    >
                                                        {{ t('common.accounts') }}
                                                    </p>
                                                    <button
                                                        v-for="(account, idx) in searchResults"
                                                        :key="account.id"
                                                        @mousedown.prevent="selectAccount(account)"
                                                        class="w-full flex items-center gap-3 px-3 py-2.5 transition-colors border-t border-gray-50 dark:border-gray-800"
                                                        :class="
                                                            accountDropdownIndex === idx
                                                                ? 'bg-primary/8 dark:bg-primary/10'
                                                                : 'hover:bg-gray-50 dark:hover:bg-gray-800/60'
                                                        "
                                                    >
                                                        <img
                                                            :src="account.avatar"
                                                            :alt="account.username"
                                                            class="w-9 h-9 rounded-full object-cover flex-shrink-0"
                                                        />
                                                        <div class="flex-1 min-w-0 text-left">
                                                            <p
                                                                class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                                            >
                                                                {{
                                                                    account.name || account.username
                                                                }}
                                                            </p>
                                                            <p
                                                                class="text-xs text-gray-400 truncate"
                                                            >
                                                                @{{ account.username
                                                                }}<span
                                                                    v-if="account.domain"
                                                                    class="text-gray-300 dark:text-gray-600"
                                                                    >@{{ account.domain }}</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <span
                                                            class="flex-shrink-0 text-xs font-bold text-primary flex items-center gap-1"
                                                        >
                                                            <PlusIcon class="w-3.5 h-3.5" />
                                                            {{ t('common.add') }}
                                                        </span>
                                                    </button>
                                                </div>

                                                <div v-else class="py-10 text-center">
                                                    <div
                                                        class="w-10 h-10 mx-auto mb-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center"
                                                    >
                                                        <MagnifyingGlassIcon
                                                            class="w-5 h-5 text-gray-300 dark:text-gray-600"
                                                        />
                                                    </div>
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-400"
                                                    >
                                                        {{ t('common.noAccountsFoundFor') }} "<span
                                                            class="font-semibold"
                                                            >{{ searchQuery }}</span
                                                        >"
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        {{
                                                            t(
                                                                'common.tryADifferentUsernameOrHandle'
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </Transition>
                                    </div>

                                    <div v-if="form.selectedAccounts.length > 0">
                                        <TransitionGroup name="row" tag="div" class="space-y-2">
                                            <div
                                                v-for="account in form.selectedAccounts"
                                                :key="account.id"
                                                class="group flex items-center gap-3.5 p-3 bg-[#FAFAFA] dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/60 rounded-xl hover:border-gray-200 dark:hover:border-gray-700 transition-all"
                                            >
                                                <img
                                                    :src="account.avatar"
                                                    :alt="account.username"
                                                    class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-2 ring-white dark:ring-gray-800"
                                                />
                                                <div class="flex-1 min-w-0">
                                                    <p
                                                        class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight"
                                                    >
                                                        {{ account.name }}
                                                    </p>
                                                    <p
                                                        class="text-xs text-gray-400 truncate mt-0.5"
                                                    >
                                                        @{{ account.username }}
                                                    </p>
                                                </div>
                                                <div
                                                    v-if="account.is_owner"
                                                    class="hidden sm:flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex-shrink-0"
                                                >
                                                    <CheckCircleIcon class="w-3.5 h-3.5" />
                                                    {{ t('common.approved') }}
                                                </div>
                                                <div
                                                    v-else
                                                    class="hidden sm:flex items-center gap-1 text-xs font-medium text-gray-400 flex-shrink-0"
                                                >
                                                    <ClockIcon class="w-3.5 h-3.5" />
                                                    {{ t('common.pending') }}
                                                </div>
                                                <button
                                                    @click="removeAccount(account.id)"
                                                    class="p-1.5 rounded-xl text-gray-300 dark:text-gray-600 opacity-0 group-hover:opacity-100 hover:bg-red-50 dark:hover:bg-red-950/40 hover:text-red-500 transition-all"
                                                >
                                                    <TrashIcon class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </TransitionGroup>
                                    </div>

                                    <div v-else class="py-12 text-center">
                                        <div
                                            class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center"
                                        >
                                            <UsersIcon
                                                class="w-6 h-6 text-gray-300 dark:text-gray-600"
                                            />
                                        </div>
                                        <p
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            {{ t('common.searchForAccountsToAdd') }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ t('common.atLeastOneAccountIsRequiredToPublish') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="h-50"></div>
                        </div>

                        <div class="space-y-5">
                            <div
                                class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                            >
                                <h3
                                    class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider mb-4"
                                >
                                    {{ t('common.checklist') }}
                                </h3>
                                <div class="space-y-2.5">
                                    <div
                                        v-for="item in checklist"
                                        :key="item.label"
                                        class="flex items-center gap-3 text-sm"
                                        :class="
                                            item.done
                                                ? 'text-gray-600 dark:text-gray-300'
                                                : 'text-gray-400'
                                        "
                                    >
                                        <div
                                            class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 transition-all"
                                            :class="
                                                item.done
                                                    ? 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400'
                                                    : 'border-2 border-gray-200 dark:border-gray-700'
                                            "
                                        >
                                            <CheckIcon v-if="item.done" class="w-3 h-3" />
                                        </div>
                                        <span
                                            :class="item.done ? 'line-through text-gray-400' : ''"
                                            >{{ item.label }}</span
                                        >
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-800">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-xs text-gray-400">Ready to publish</span>
                                        <span
                                            class="text-xs font-bold text-gray-600 dark:text-gray-300 tabular-nums"
                                            >{{ doneCount }}/{{ checklist.length }}</span
                                        >
                                    </div>
                                    <div
                                        class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden"
                                    >
                                        <div
                                            class="h-1.5 rounded-full transition-all duration-500"
                                            :class="
                                                isFormValid
                                                    ? 'bg-primary'
                                                    : 'bg-gray-300 dark:bg-gray-600'
                                            "
                                            :style="{
                                                width: `${(doneCount / checklist.length) * 100}%`
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <div
                                    v-if="isFormValid"
                                    class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-800"
                                >
                                    <button
                                        @click="saveStarterKit"
                                        :disabled="!isFormValid || isSaving"
                                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold transition-all"
                                        :class="
                                            isFormValid && !isSaving
                                                ? 'bg-primary hover:bg-[#D91B42] text-white shadow-lg shadow-primary/25'
                                                : 'bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-not-allowed'
                                        "
                                    >
                                        <Spinner v-if="isSaving" size="sm" />
                                        <RocketLaunchIcon v-else class="w-4 h-4" />
                                        {{
                                            isSaving
                                                ? t('common.publishing') + '…'
                                                : t('common.publishKit')
                                        }}
                                    </button>
                                </div>
                            </div>

                            <div
                                class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 space-y-4"
                            >
                                <h3
                                    class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider"
                                >
                                    {{ t('nav.settings') }}
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
                                        @click="form.sensitive = !form.sensitive"
                                        role="switch"
                                        :aria-checked="form.discoverable"
                                        :class="[
                                            'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none flex-shrink-0',
                                            form.sensitive
                                                ? 'bg-primary'
                                                : 'bg-gray-200 dark:bg-gray-700'
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform',
                                                form.sensitive ? 'translate-x-6' : 'translate-x-1'
                                            ]"
                                        />
                                    </button>
                                </div>

                                <div class="border-t border-gray-50 dark:border-gray-800 pt-4">
                                    <button
                                        @click="form.termsOfService = !form.termsOfService"
                                        class="flex items-start gap-3 w-full text-left group"
                                    >
                                        <div
                                            class="w-5 h-5 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 transition-all border-2"
                                            :class="
                                                form.termsOfService
                                                    ? 'bg-primary border-primary text-white'
                                                    : 'border-gray-200 dark:border-gray-700 group-hover:border-primary/40'
                                            "
                                        >
                                            <CheckIcon v-if="form.termsOfService" class="w-3 h-3" />
                                        </div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed"
                                        >
                                            {{ t('common.thisKitAbidesByThe') }}
                                            <router-link
                                                to="/community-guidelines"
                                                class="font-semibold text-primary hover:underline"
                                                @click.stop
                                            >
                                                {{ t('auth.communityGuidelines') }}
                                            </router-link>
                                            {{ t('common.and') }}
                                            <router-link
                                                to="/terms"
                                                class="font-semibold text-primary hover:underline"
                                                @click.stop
                                            >
                                                {{ t('auth.termsOfService') }}
                                            </router-link>
                                        </p>
                                    </button>
                                </div>
                            </div>

                            <div
                                class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                            >
                                <h3
                                    class="font-display font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider mb-4"
                                >
                                    {{ t('common.preview') }}
                                </h3>
                                <div
                                    class="rounded-xl border border-gray-100 dark:border-gray-800 p-4 bg-gray-50 dark:bg-gray-800/30"
                                >
                                    <div class="flex items-center mb-3">
                                        <template v-if="form.selectedAccounts.length > 0">
                                            <img
                                                v-for="(a, i) in form.selectedAccounts.slice(0, 4)"
                                                :key="a.id"
                                                :src="a.avatar"
                                                class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-900 object-cover -ml-2 first:ml-0 shadow-sm"
                                                :style="{ zIndex: 4 - i }"
                                            />
                                            <span
                                                v-if="form.selectedAccounts.length > 4"
                                                class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-900 bg-gray-100 dark:bg-gray-700 -ml-2 flex items-center justify-center text-[10px] font-bold text-gray-400 flex-shrink-0"
                                            >
                                                +{{ form.selectedAccounts.length - 4 }}
                                            </span>
                                        </template>
                                        <div v-else class="flex">
                                            <div
                                                v-for="i in 3"
                                                :key="i"
                                                class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-900 bg-gray-100 dark:bg-gray-700 -ml-2 first:ml-0"
                                            ></div>
                                        </div>
                                    </div>
                                    <p
                                        class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate"
                                    >
                                        {{ form.name || 'Kit Name…' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1 line-clamp-2">
                                        {{
                                            form.description ||
                                            t('common.yourDescriptionWillAppearHere') + '.'
                                        }}
                                    </p>
                                    <div
                                        v-if="form.hashtags.length > 0"
                                        class="flex flex-wrap gap-1 mt-2.5"
                                    >
                                        <span
                                            v-for="tag in form.hashtags.slice(0, 3)"
                                            :key="tag"
                                            class="text-[10px] font-medium px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full"
                                        >
                                            #{{ tag }}
                                        </span>
                                    </div>
                                    <div
                                        class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-700"
                                    >
                                        <span class="text-xs text-gray-400"
                                            >{{ form.selectedAccounts.length }}
                                            {{ t('common.accounts') }}</span
                                        >
                                        <span class="text-xs font-bold text-primary"
                                            >{{ t('common.useKit') }} →</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import AnimatedButton from '@/components/AnimatedButton.vue'
import MainLayout from '@/layouts/MainLayout.vue'
import {
    CheckCircleIcon,
    ClockIcon,
    ExclamationCircleIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    TrashIcon,
    UsersIcon,
    WalletIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'
import { CheckIcon, ChevronLeftIcon, RocketLaunchIcon } from '@heroicons/vue/24/solid'
import { useAlertModal } from '@/composables/useAlertModal.js'
import { ref, computed, onMounted, onUnmounted, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from '~/plugins/axios'
import { useRouter } from 'vue-router'
import KitFeatureDisabled from '@/components/StarterKits/KitFeatureDisabled.vue'
const api = axios.getAxiosInstance()

const { alertModal } = useAlertModal()
const { t } = useI18n()

const appConfig = inject('appConfig')

const isSaving = ref(false)
const hashtagInput = ref('')
const searchQuery = ref('')
const isSearching = ref(false)
const searchResults = ref([])
const config = ref()
const canCreate = ref(false)
const loading = ref(true)
const isDisabled = ref(false)
const router = useRouter()

const accountSearchInputRef = ref(null)
const accountSearchContainerRef = ref(null)
const hashtagInputRef = ref(null)
const hashtagContainerRef = ref(null)

const showAccountDropdown = ref(false)
const accountDropdownIndex = ref(-1)
const showHashtagDropdown = ref(false)
const hashtagDropdownIndex = ref(-1)
const hashtagSuggestions = ref([])
const MAX_ACCOUNTS = 25

let searchDebounceTimer = null
let hashtagDebounceTimer = null

const form = ref({
    name: '',
    description: '',
    hashtags: [],
    visibility: 6,
    selectedAccounts: [],
    sensitive: false,
    termsOfService: false
})

const checklist = computed(() => [
    { label: t('common.addAName'), done: form.value.name.trim().length > 0 },
    { label: t('common.writeADescription'), done: form.value.description.trim().length > 0 },
    { label: t('common.addAtLeastOneHashtag'), done: form.value.hashtags.length > 0 },
    { label: t('common.addAtLeastOneAccount'), done: form.value.selectedAccounts.length > 0 },
    { label: t('common.acceptCommunityGuidelines'), done: form.value.termsOfService }
])
const doneCount = computed(() => checklist.value.filter((c) => c.done).length)
const isFormValid = computed(() => checklist.value.every((c) => c.done))
const accountLimitReached = computed(() => form.value.selectedAccounts.length >= MAX_ACCOUNTS)

const onAccountSearchInput = () => {
    accountDropdownIndex.value = -1
    clearTimeout(searchDebounceTimer)
    if (!searchQuery.value.trim()) {
        showAccountDropdown.value = false
        searchResults.value = []
        isSearching.value = false
        return
    }
    showAccountDropdown.value = true
    isSearching.value = true
    searchDebounceTimer = setTimeout(() => fetchAccountSearch(searchQuery.value.trim()), 350)
}

const onAccountSearchFocus = () => {
    if (searchQuery.value.trim() && searchResults.value.length > 0) showAccountDropdown.value = true
}

const fetchAccountSearch = async (query) => {
    try {
        const { data } = await api.post('/api/v1/starter-kits/compose/search/accounts', {
            q: query
        })
        const selectedIds = new Set(form.value.selectedAccounts.map((a) => a.id))
        searchResults.value = (data?.data ?? data ?? []).filter((a) => !selectedIds.has(a.id))
    } catch (err) {
        searchResults.value = []
    } finally {
        isSearching.value = false
    }
}

const navigateAccountDropdown = (dir) => {
    if (!showAccountDropdown.value || !searchResults.value.length) return
    const max = searchResults.value.length - 1
    accountDropdownIndex.value = Math.min(Math.max(accountDropdownIndex.value + dir, 0), max)
}

const selectHighlightedAccount = () => {
    if (accountDropdownIndex.value >= 0 && searchResults.value[accountDropdownIndex.value]) {
        selectAccount(searchResults.value[accountDropdownIndex.value])
    }
}

const selectAccount = (account) => {
    if (accountLimitReached.value) {
        return
    }

    if (!form.value.selectedAccounts.find((a) => a.id === account.id)) {
        form.value.selectedAccounts.push(account)
    }

    searchResults.value = searchResults.value.filter((a) => a.id !== account.id)
    searchQuery.value = ''
    closeAccountDropdown()
}

const closeAccountDropdown = () => {
    showAccountDropdown.value = false
    accountDropdownIndex.value = -1
}
const clearSearch = () => {
    searchQuery.value = ''
    searchResults.value = []
    closeAccountDropdown()
    accountSearchInputRef.value?.focus()
}
const removeAccount = (id) => {
    form.value.selectedAccounts = form.value.selectedAccounts.filter((a) => a.id !== id)
}
const clearSelectedAccounts = () => {
    form.value.selectedAccounts = []
}

const onHashtagFocus = () => {
    if (hashtagInput.value.trim() && hashtagSuggestions.value.length > 0)
        showHashtagDropdown.value = true
}

const onHashtagInput = () => {
    hashtagDropdownIndex.value = -1
    clearTimeout(hashtagDebounceTimer)
    const query = hashtagInput.value.trim().replace(/^#/, '')
    if (!query) {
        showHashtagDropdown.value = false
        hashtagSuggestions.value = []
        return
    }
    hashtagDebounceTimer = setTimeout(() => fetchHashtagSuggestions(query), 300)
}

const fetchHashtagSuggestions = async (query) => {
    try {
        const { data } = await api.post('/api/v1/starter-kits/compose/search/hashtags', {
            q: query
        })
        hashtagSuggestions.value = data.data.filter((t) => !form.value.hashtags.includes(t.slug))
        showHashtagDropdown.value = hashtagSuggestions.value.length > 0
    } catch {
        hashtagSuggestions.value = []
    }
}

const navigateHashtagDropdown = (dir) => {
    if (!showHashtagDropdown.value || !hashtagSuggestions.value.length) return
    const max = hashtagSuggestions.value.length - 1
    hashtagDropdownIndex.value = Math.min(Math.max(hashtagDropdownIndex.value + dir, 0), max)
}

const selectHashtagSuggestion = (tag) => {
    const slug = tag.slug.trim().replace(/^#/, '')
    if (slug && !form.value.hashtags.includes(slug) && form.value.hashtags.length < 10) {
        form.value.hashtags.push(slug)
        hashtagInput.value = ''
        hashtagSuggestions.value = []
        showHashtagDropdown.value = false
    }
}

const closeHashtagDropdown = () => {
    showHashtagDropdown.value = false
    hashtagDropdownIndex.value = -1
}

const addHashtag = () => {
    const tag = hashtagInput.value.trim().replace(/^#/, '')
    if (tag && !form.value.hashtags.includes(tag) && form.value.hashtags.length < 10) {
        form.value.hashtags.push(tag)
        hashtagInput.value = ''
        hashtagSuggestions.value = []
        showHashtagDropdown.value = false
    }
}

const removeHashtag = (index) => {
    form.value.hashtags.splice(index, 1)
}

const handleClickOutside = (e) => {
    if (accountSearchContainerRef.value && !accountSearchContainerRef.value.contains(e.target))
        closeAccountDropdown()
    if (hashtagContainerRef.value && !hashtagContainerRef.value.contains(e.target))
        closeHashtagDropdown()
}

onMounted(() => {
    if (appConfig.starterKits === false) {
        isDisabled.value = true
        loading.value = false
        return
    }
    document.addEventListener('mousedown', handleClickOutside)
    fetchConfig()

    const params = new URLSearchParams(window.location.search)
    const tag = params.get('tag')?.trim().replace(/^#/, '')
    if (tag && !form.value.hashtags.includes(tag)) {
        form.value.hashtags.push(tag)
    }
})

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside)
    clearTimeout(searchDebounceTimer)
    clearTimeout(hashtagDebounceTimer)
})

const saveStarterKit = async () => {
    if (!isFormValid.value) {
        return
    }
    isSaving.value = true
    try {
        const res = await api.post('/api/v1/starter-kits/create', {
            title: form.value.name,
            description: form.value.description,
            hashtags: form.value.hashtags,
            visibility: form.value.visibility,
            sensitive: form.value.sensitive,
            account_ids: form.value.selectedAccounts.map((a) => a.id),
            terms_of_service: form.value.termsOfService
        })
        if (res.data.data.url) window.location.href = res.data.data.url
    } catch (err) {
        await alertModal(
            '⚠️ ' + t('common.somethingWentWrong'),
            err?.response?.data?.error?.message ||
                err?.response?.data?.message ||
                t('common.unexpectedError')
        )
    } finally {
        isSaving.value = false
    }
}

const fetchConfig = async () => {
    try {
        const res = await api.get('/api/v1/starter-kits/self/config')
        config.value = res.data.data
        canCreate.value = res.data.data.can_create
    } catch (e) {
        if (e?.response?.status == 404) {
            isDisabled.value = true
        }
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
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

.row-leave-active {
    position: absolute;
}

.dropdown-enter-active {
    transition:
        opacity 0.15s ease,
        transform 0.15s ease;
}

.dropdown-leave-active {
    transition:
        opacity 0.1s ease,
        transform 0.1s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(4px);
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
