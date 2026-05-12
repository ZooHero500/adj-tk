<template>
    <MainLayout>
        <div v-if="loading" class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body">
            <div class="max-w-5xl mx-auto px-6 md:px-12 pt-14">
                <div
                    class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded-full animate-pulse mb-10"
                ></div>
                <div
                    class="h-8 w-2/3 bg-gray-200 dark:bg-gray-800 rounded-xl animate-pulse mb-3"
                ></div>
                <div
                    class="h-4 w-full bg-gray-200 dark:bg-gray-800 rounded-full animate-pulse mb-2"
                ></div>
                <div
                    class="h-4 w-1/2 bg-gray-200 dark:bg-gray-800 rounded-full animate-pulse mb-8"
                ></div>
                <div class="flex gap-2 mb-12">
                    <div
                        class="h-6 w-16 bg-gray-200 dark:bg-gray-800 rounded-full animate-pulse"
                    ></div>
                    <div
                        class="h-6 w-14 bg-gray-200 dark:bg-gray-800 rounded-full animate-pulse"
                    ></div>
                </div>
                <div class="space-y-2">
                    <div
                        v-for="i in 8"
                        :key="i"
                        class="flex items-center gap-4 p-4 bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl animate-pulse"
                        :style="`animation-delay: ${i * 50}ms`"
                    >
                        <div
                            class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 flex-shrink-0"
                        ></div>
                        <div class="flex-1">
                            <div class="h-3.5 w-32 bg-gray-200 dark:bg-gray-800 rounded mb-2"></div>
                            <div class="h-3 w-20 bg-gray-200 dark:bg-gray-800 rounded"></div>
                        </div>
                        <div
                            class="h-3 w-24 bg-gray-200 dark:bg-gray-800 rounded hidden sm:block"
                        ></div>
                        <div class="h-8 w-16 bg-gray-200 dark:bg-gray-800 rounded-xl"></div>
                    </div>
                </div>
            </div>
        </div>

        <KitFeatureDisabled v-else-if="isDisabled && !loading" />

        <div
            v-else-if="error"
            class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] flex items-center justify-center px-6 font-body"
        >
            <div class="text-center max-w-sm">
                <div
                    class="w-16 h-16 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900/50 flex items-center justify-center mx-auto mb-5"
                >
                    <svg
                        class="w-7 h-7 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                        />
                    </svg>
                </div>
                <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white mb-2">
                    {{
                        error === 'not_found'
                            ? $t('common.kitNotFound')
                            : $t('common.somethingWentWrong')
                    }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                    {{
                        error === 'not_found'
                            ? $t('common.thisStarterKitDoesNotExistOrMayHaveBeenRemoved')
                            : $t('common.weCouldNotLoadThisStarterKitPleaseTryAgain')
                    }}
                </p>
                <div class="flex gap-3 justify-center">
                    <button
                        @click="retryLoad"
                        class="px-5 py-2.5 bg-primary hover:bg-[#D91B42] text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-primary/25"
                    >
                        {{ $t('common.tryAgain') }}
                    </button>
                    <router-link
                        to="/starter-kits"
                        class="px-5 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-xl transition-all"
                    >
                        {{ $t('common.browseKits') }}
                    </router-link>
                </div>
            </div>
        </div>

        <KitSensitiveWarning
            v-else-if="isSensitiveBlocked"
            @confirm="showSensitiveContent = true"
        />

        <div v-else class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body lg:-mt-5 lg:-mr-2">
            <div v-if="starterKit.header_url" class="relative w-full">
                <img
                    :src="starterKit.header_url"
                    :alt="starterKit.title"
                    class="w-full aspect-[5/2] object-cover"
                />
                <div
                    v-if="starterKit.icon_url"
                    class="absolute bottom-0 translate-y-1/2 inset-x-0 pointer-events-none"
                >
                    <div class="max-w-5xl mx-auto px-6 md:px-12">
                        <img
                            :src="starterKit.icon_url"
                            :alt="starterKit.title"
                            class="bg-white dark:bg-gray-900 p-3 w-23 h-23 lg:w-30 lg:h-30 xl:w-40 xl:h-40 rounded-2xl lg:rounded-3xl xl:rounded-4xl object-cover border-4 border-[#FAFAFA] dark:border-[#0A0A0A] shadow-xl pointer-events-auto"
                        />
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 md:px-12">
                <div
                    :class="[
                        'flex items-center justify-between',
                        starterKit.header_url && starterKit.icon_url
                            ? 'pt-14 lg:pt-18 xl:pt-24 pb-5'
                            : starterKit.header_url
                              ? 'pt-5 pb-5'
                              : 'pt-10 pb-5'
                    ]"
                >
                    <router-link
                        to="/starter-kits"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors group"
                    >
                        <ChevronLeftIcon
                            class="w-4 h-4 transition-transform group-hover:-translate-x-0.5"
                        />
                        {{ $t('common.starterKits') }}
                    </router-link>

                    <div class="flex items-center gap-2">
                        <AnimatedButton
                            v-if="starterKit.status === 10"
                            size="xs"
                            @click="copyShareUrl"
                            variant="outline"
                            :disabled="copied"
                            :pill="true"
                        >
                            <div class="flex items-center justify-center gap-1.5">
                                <ShareIcon class="w-4 h-4" />
                                {{ copied ? $t('post.copiedExclamation') : $t('common.share') }}
                            </div>
                        </AnimatedButton>

                        <AnimatedButton
                            v-if="starterKit.is_owner"
                            @click="editKit"
                            size="xs"
                            variant="primaryOutline"
                            :pill="true"
                        >
                            <div class="flex items-center justify-center gap-1.5">
                                <PencilIcon class="w-3.5 h-3.5" />
                                {{ $t('common.editKit') }}
                            </div>
                        </AnimatedButton>

                        <AnimatedButton
                            v-else
                            @click="handleReport"
                            size="xs"
                            variant="primaryOutline"
                            :pill="true"
                        >
                            <div class="flex items-center justify-center gap-1.5">
                                <FlagIcon class="w-3.5 h-3.5" />
                                {{ $t('common.report') }}
                            </div>
                        </AnimatedButton>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-start gap-10 mb-10">
                    <div class="flex-1 min-w-0">
                        <div v-if="starterKit.icon_url && !starterKit.header_url" class="mb-4">
                            <img
                                :src="starterKit.icon_url"
                                :alt="starterKit.title"
                                class="w-14 h-14 rounded-2xl object-cover border border-gray-200 dark:border-gray-700 shadow-sm"
                            />
                        </div>

                        <div class="flex items-center gap-2.5 mb-3">
                            <span class="block w-8 h-px bg-primary"></span>
                            <span
                                class="text-xs font-semibold tracking-[0.15em] uppercase text-primary"
                                >{{ $t('common.starterKit') }}</span
                            >
                            <span
                                v-if="starterKit.is_owner"
                                :class="[
                                    'ml-1 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border',
                                    starterKit.status === 10
                                        ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
                                        : starterKit.status === 5
                                          ? 'bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800'
                                          : 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800'
                                ]"
                            >
                                <span
                                    :class="[
                                        'w-1.5 h-1.5 rounded-full',
                                        starterKit.status === 10
                                            ? 'bg-emerald-500 animate-pulse'
                                            : starterKit.status === 5
                                              ? 'bg-red-500 animate-pulse'
                                              : 'bg-amber-500 animate-pulse'
                                    ]"
                                ></span>
                                {{ starterKit.status_text }}
                            </span>
                        </div>

                        <h1
                            class="font-display text-4xl md:text-5xl font-bold text-gray-950 dark:text-white leading-tight tracking-tight mb-3"
                        >
                            {{ starterKit.title }}
                        </h1>
                        <p
                            class="text-gray-500 dark:text-gray-400 leading-relaxed max-w-2xl mb-5 text-base"
                        >
                            {{ starterKit.description }}
                        </p>
                        <div class="flex flex-wrap gap-1.5 mb-8">
                            <router-link
                                v-for="tag in starterKit.hashtags"
                                :key="tag"
                                :to="`/starter-kits/hashtag/${tag}`"
                                class="px-2.5 py-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-primary/40 hover:text-primary text-gray-500 dark:text-gray-400 rounded-full text-xs font-medium transition-all"
                            >
                                #{{ tag }}
                            </router-link>
                        </div>

                        <div
                            class="flex flex-wrap items-center gap-x-5 gap-y-3 pt-6 border-t border-gray-100 dark:border-gray-800"
                        >
                            <div class="flex items-baseline gap-1.5">
                                <span
                                    class="text-lg font-bold font-display text-gray-900 dark:text-white"
                                    >{{ starterKit.approved_accounts }}</span
                                >
                                <span class="text-xs text-gray-400">{{
                                    $t('common.accounts')
                                }}</span>
                            </div>
                            <div class="w-px h-4 bg-gray-200 dark:bg-gray-800"></div>
                            <div class="flex items-baseline gap-1.5">
                                <span
                                    class="text-lg font-bold font-display text-gray-900 dark:text-white"
                                    >{{ formatCount(starterKit.uses) }}</span
                                >
                                <span class="text-xs text-gray-400">{{ $t('common.uses') }}</span>
                            </div>
                            <div class="w-px h-4 bg-gray-200 dark:bg-gray-800"></div>
                            <div class="flex items-baseline gap-1.5">
                                <span
                                    class="text-lg font-bold font-display text-gray-900 dark:text-white"
                                    >{{ formatCount(totalFollowers) }}</span
                                >
                                <span class="text-xs text-gray-400">{{
                                    $t('common.totalReach')
                                }}</span>
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-xs text-gray-400">curated by</span>
                                <router-link
                                    :to="`/@${starterKit.creator.username}`"
                                    class="flex items-center gap-1.5 hover:text-primary transition-colors"
                                >
                                    <img
                                        :src="starterKit.creator.avatar"
                                        class="w-5 h-5 rounded-full"
                                    />
                                    <span
                                        class="text-xs font-semibold text-gray-700 dark:text-gray-200"
                                        >@{{ starterKit.creator.username }}</span
                                    >
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:block flex-shrink-0 w-70">
                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 sticky top-6"
                        >
                            <div class="flex items-center mb-4">
                                <img
                                    v-for="(account, i) in filteredAccounts.slice(0, 5)"
                                    :key="account.id"
                                    :src="account.avatar"
                                    class="w-9 h-9 rounded-full border-2 border-white dark:border-gray-900 object-cover -ml-2 first:ml-0 shadow-sm"
                                    :style="{ zIndex: 5 - i }"
                                />
                                <span
                                    v-if="starterKit.approved_accounts > 5"
                                    class="w-9 h-9 rounded-full border-2 border-white dark:border-gray-900 bg-gray-100 dark:bg-gray-800 -ml-2 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400 shadow-sm flex-shrink-0"
                                >
                                    +{{ starterKit.approved_accounts - 5 }}
                                </span>
                            </div>

                            <div>
                                <div
                                    class="text-sm font-semibold text-gray-900 dark:text-white mb-0.5"
                                >
                                    {{ starterKit.approved_accounts }} {{ $t('common.accounts') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ formatCount(totalFollowers) }}
                                    {{ $t('common.combinedFollowers') }}
                                </div>
                            </div>

                            <template v-if="!starterKit.is_owner">
                                <template v-if="starterKit.status === 10">
                                    <div
                                        v-if="hasUsed && newAccountsInKit.length > 0"
                                        class="mt-4 flex items-start gap-2.5 p-3 bg-primary/5 border border-primary/20 rounded-xl"
                                    >
                                        <span class="mt-0.5 w-4 h-4 flex-shrink-0 text-primary">
                                            <UserPlusIcon class="w-4 h-4" />
                                        </span>
                                        <p
                                            class="text-xs text-gray-600 dark:text-gray-300 leading-snug"
                                        >
                                            <span class="font-semibold text-primary"
                                                >{{ newAccountsInKit.length }} new
                                                {{
                                                    newAccountsInKit.length === 1
                                                        ? 'account'
                                                        : 'accounts'
                                                }}</span
                                            >
                                            added since you last used this kit.
                                        </p>
                                    </div>

                                    <AnimatedButton
                                        v-if="hasUsed && newAccountsInKit.length > 0"
                                        class="w-full mt-3"
                                        @click="reuseStarterKit"
                                        :disabled="isReusingKit || reuseSuccess"
                                        :loading="isReusingKit"
                                    >
                                        <div
                                            v-if="reuseSuccess"
                                            class="flex items-center justify-center gap-2"
                                        >
                                            <CheckCircleIcon class="w-4 h-4" />
                                            {{ $t('common.done') }}!
                                        </div>
                                        <div
                                            v-else-if="isReusingKit"
                                            class="flex items-center justify-center gap-2"
                                        >
                                            <Spinner size="sm" theme="slate" />
                                            {{ $t('common.following') }}...
                                        </div>
                                        <div v-else class="flex items-center justify-center gap-2">
                                            <UserPlusIcon class="w-4 h-4" />
                                            Follow {{ newAccountsInKit.length }} new
                                            {{
                                                newAccountsInKit.length === 1
                                                    ? 'account'
                                                    : 'accounts'
                                            }}
                                        </div>
                                    </AnimatedButton>

                                    <AnimatedButton
                                        v-else-if="hasUsed"
                                        class="w-full mt-5"
                                        :disabled="true"
                                        variant="light"
                                    >
                                        <div class="flex items-center justify-center gap-2">
                                            <CheckCircleIcon class="w-4 h-4" />
                                            {{ $t('common.alreadyUsed') }}
                                        </div>
                                    </AnimatedButton>

                                    <AnimatedButton
                                        v-else
                                        class="w-full mt-5"
                                        @click="useStarterKit"
                                        :disabled="isUsing || useSuccess || limitRestriction"
                                        :loading="isUsing"
                                    >
                                        <div
                                            v-if="useSuccess"
                                            class="flex items-center justify-center gap-2"
                                        >
                                            <CheckCircleIcon class="w-4 h-4" />
                                            {{ $t('common.kitUsed') }}!
                                        </div>
                                        <div
                                            v-else-if="isUsing"
                                            class="flex items-center justify-center gap-2"
                                        >
                                            <Spinner size="sm" theme="slate" />
                                            {{ $t('common.following') }}...
                                        </div>
                                        <div v-else-if="limitRestriction">
                                            {{ $t('common.youCannotUseThisKit') }}
                                        </div>
                                        <div v-else class="flex items-center justify-center gap-2">
                                            <UserGroupIcon class="w-4 h-4" />
                                            {{ $t('common.useKit') }}
                                        </div>
                                    </AnimatedButton>
                                </template>

                                <AnimatedButton
                                    v-else
                                    class="w-full mt-5"
                                    :disabled="true"
                                    variant="light"
                                >
                                    {{ $t('common.pendingApproval') }}
                                </AnimatedButton>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="lg:hidden mb-8">
                    <template v-if="!starterKit.is_owner">
                        <button
                            v-if="starterKit.status === 10"
                            @click="useStarterKit"
                            :disabled="isUsing || useSuccess || hasUsed || limitRestriction"
                            class="w-full flex items-center justify-center gap-2 py-3 bg-primary hover:bg-[#D91B42] disabled:bg-gray-200 dark:disabled:bg-gray-700 text-white font-semibold text-sm rounded-xl transition-all active:scale-[0.98] shadow-md shadow-primary/25"
                        >
                            <CheckCircleIcon v-if="hasUsed || useSuccess" class="w-4 h-4" />
                            <UserPlusIcon v-else-if="!isUsing" class="w-4 h-4" />
                            <Spinner v-else size="sm" />
                            {{
                                hasUsed
                                    ? $t('common.used')
                                    : useSuccess
                                      ? $t('common.done') + '!'
                                      : isUsing
                                        ? $t('common.following') + '...'
                                        : $t('common.useKit')
                            }}
                        </button>
                        <button
                            v-else
                            disabled
                            class="w-full py-3 bg-gray-100 dark:bg-gray-800 text-gray-400 font-semibold text-sm rounded-xl"
                        >
                            {{ $t('common.pendingApproval') }}
                        </button>
                    </template>
                </div>

                <div class="pb-12">
                    <div v-if="starterKit.is_owner" class="flex items-center justify-between mb-5">
                        <div
                            class="flex gap-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-1"
                        >
                            <button
                                v-for="tab in ownerTabs"
                                :key="tab.value"
                                @click="activeTab = tab.value"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5"
                                :class="
                                    activeTab === tab.value
                                        ? 'bg-primary text-white shadow-sm'
                                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                                "
                            >
                                {{ tab.label }}
                                <span
                                    class="px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="
                                        activeTab === tab.value
                                            ? 'bg-white/20 text-white'
                                            : 'bg-gray-100 dark:bg-gray-800 text-gray-500'
                                    "
                                >
                                    {{ tabCount(tab.value) }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <div v-else class="flex items-center justify-between mb-5">
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">
                            {{ filteredAccounts.length }} {{ $t('common.accounts') }}
                        </h2>
                    </div>

                    <div
                        v-if="filteredAccounts.length === 0"
                        class="py-16 text-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-2xl"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3"
                        >
                            <UsersIcon class="w-5 h-5 text-gray-400" />
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <template v-if="activeTab === 0">{{
                                $t('common.noPendingAccounts')
                            }}</template>
                            <template v-else-if="activeTab === 2">{{
                                $t('common.noRejectedAccounts')
                            }}</template>
                            <template v-else>{{ $t('common.noAccountsInThisKitYet') }}</template>
                        </p>
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="(account, index) in filteredAccounts"
                            :key="account.id"
                            class="group flex items-center gap-4 p-3.5 bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-md hover:shadow-black/5 transition-all"
                        >
                            <span
                                class="hidden sm:block text-xs font-semibold text-gray-300 dark:text-gray-700 w-5 text-center flex-shrink-0 tabular-nums"
                            >
                                {{ index + 1 }}
                            </span>

                            <img
                                :src="account.avatar"
                                :alt="account.name"
                                class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-100 dark:ring-gray-800"
                            />

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p
                                        class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight"
                                    >
                                        {{ account.name }}
                                    </p>
                                    <template v-if="isFollowedByKit(account)">
                                        <span
                                            v-if="account.id === authStore.getUser.id"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/60 rounded-full text-[10px] font-bold"
                                        >
                                            <UserIcon class="w-3 h-3" /> My Account
                                        </span>

                                        <span
                                            v-else
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 rounded-full text-[10px] font-bold"
                                        >
                                            <CheckCircleIcon class="w-3 h-3" />
                                            {{ $t('common.following') }}
                                        </span>
                                    </template>
                                    <span
                                        v-else-if="hasUsed && account.kit_status === 1"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-500 dark:text-blue-400 border border-blue-200 dark:border-blue-800/60 rounded-full text-[10px] font-bold"
                                    >
                                        <UserPlusIcon class="w-3 h-3" /> New
                                    </span>
                                    <span
                                        v-if="starterKit.is_owner && account.kit_status === 0"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 rounded-full text-[10px] font-bold"
                                    >
                                        <ClockIcon class="w-3 h-3" /> {{ $t('common.pending') }}
                                    </span>
                                    <span
                                        v-else-if="starterKit.is_owner && account.kit_status === 2"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/60 rounded-full text-[10px] font-bold"
                                    >
                                        <XCircleIcon class="w-3 h-3" /> {{ $t('common.rejected') }}
                                    </span>
                                </div>
                                <p
                                    class="text-xs text-gray-400 dark:text-gray-500 leading-tight mt-0.5 truncate"
                                >
                                    @{{ account.username }}
                                </p>
                            </div>

                            <p
                                class="hidden xl:block text-xs text-gray-400 dark:text-gray-500 truncate flex-1 max-w-[200px]"
                            >
                                {{ account.bio?.trim() || '' }}
                            </p>

                            <div
                                class="hidden md:flex items-center gap-4 flex-shrink-0 text-xs text-gray-400"
                            >
                                <span class="flex items-center gap-1.5">
                                    <UsersIcon class="w-3.5 h-3.5" />
                                    {{ formatCount(account.follower_count) }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <VideoCameraIcon class="w-3.5 h-3.5" />
                                    {{ formatCount(account.post_count) }}
                                </span>
                            </div>

                            <AnimatedButton
                                v-if="
                                    authStore.isAuthenticated && account.id === authStore.getUser.id
                                "
                                @click="handleSelfRemove"
                                size="xs"
                                class="opacity-0 group-hover:opacity-100 transition-all"
                                variant="secondary"
                            >
                                {{ $t('common.remove') }}
                            </AnimatedButton>

                            <AnimatedButton
                                @click="handleProfileClick(account.username)"
                                size="xs"
                                class="opacity-0 group-hover:opacity-100 transition-all"
                                variant="primaryOutline"
                            >
                                {{ $t('studio.view') }} →
                            </AnimatedButton>
                        </div>

                        <div class="h-50 lg:h-20"></div>
                    </div>
                </div>
            </div>
        </div>

        <ReportModal />

        <KitFederatedModal v-model="showFederatedModal" :starter-kit="starterKit" />
    </MainLayout>
</template>

<script setup>
import { useHashids } from '@/composables/useHashids'
import MainLayout from '@/layouts/MainLayout.vue'
import axios from '@/plugins/axios'
import {
    ArrowUpTrayIcon,
    CheckCircleIcon,
    ChevronLeftIcon,
    ClipboardDocumentIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    FlagIcon,
    GlobeAltIcon,
    PencilIcon,
    ShareIcon,
    UserGroupIcon,
    UserIcon,
    UserPlusIcon,
    UsersIcon,
    VideoCameraIcon,
    XCircleIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'
import { ref, computed, onMounted, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUtils } from '@/composables/useUtils'
import { useAuthStore } from '@/stores/auth'
import AnimatedButton from '@/components/AnimatedButton.vue'
import { useReportModal } from '@/composables/useReportModal'
import { useAlertModal } from '@/composables/useAlertModal.js'
import { useI18n } from 'vue-i18n'
import KitFeatureDisabled from '@/components/StarterKits/KitFeatureDisabled.vue'
import KitSensitiveWarning from '@/components/StarterKits/KitSensitiveWarning.vue'
import AlertTriangleIcon from '@/components/Layout/AlertTriangleIcon.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const { decodeHashid } = useHashids()
const kitId = decodeHashid(route.params.id)
const authStore = useAuthStore()
const { openReportModal } = useReportModal()
const { formatCount } = useUtils()
const { alertModal, confirmModal } = useAlertModal()
const api = axios.getAxiosInstance()

const appConfig = inject('appConfig')
const starterKit = ref(null)
const loading = ref(true)
const error = ref(false)
const hasUsed = ref(false)
const isUsing = ref(false)
const limitRestriction = ref(false)
const useSuccess = ref(false)
const copied = ref(false)
const copiedFederated = ref(false)
const activeTab = ref(1)
const showFederatedModal = ref(false)
const isDisabled = ref(false)
const showSensitiveContent = ref(false)
const delta = ref([])
const isReusingKit = ref(false)
const reuseSuccess = ref(false)

const newAccountsInKit = computed(() => {
    if (!hasUsed.value || !starterKit.value?.accounts) return []
    return starterKit.value.accounts.filter(
        (a) => a.kit_status === 1 && !delta.value.includes(String(a.id))
    )
})

const isFollowedByKit = (account) => {
    return hasUsed.value && delta.value.includes(String(account.id))
}

const ownerTabs = [
    { label: t('common.active'), value: 1 },
    { label: t('common.pending'), value: 0 },
    { label: t('common.rejected'), value: 2 }
]

const tabCount = (status) => {
    if (!starterKit.value?.accounts) return 0
    return starterKit.value.accounts.filter((a) => a.kit_status === status).length
}

const filteredAccounts = computed(() => {
    if (!starterKit.value?.accounts) return []
    if (starterKit.value.is_owner) {
        return starterKit.value.accounts.filter((a) => a.kit_status === activeTab.value)
    }
    return starterKit.value.accounts.filter((a) => a.kit_status === 1)
})

const isSensitiveBlocked = computed(() => {
    return (
        starterKit.value?.is_sensitive && !starterKit.value?.is_owner && !showSensitiveContent.value
    )
})

const totalFollowers = computed(() => {
    if (!starterKit.value?.accounts) return 0
    return starterKit.value.accounts
        .filter((a) => a.kit_status === 1)
        .reduce((sum, a) => sum + parseFloat(a.follower_count || 0), 0)
})

const loadKit = async () => {
    if (appConfig.starterKits === false) {
        isDisabled.value = true
        loading.value = false
        return
    }

    try {
        if (!kitId) throw new Error('invalid_id')
        const res = await api.get(`/api/v1/starter-kits/details/${kitId}`)
        starterKit.value = res.data.data
    } catch (e) {
        error.value = [403, 404].includes(e?.response?.status) ? 'not_found' : 'generic'
        loading.value = false
        return
    } finally {
        if (authStore.isAuthenticated) {
            await loadUsed()

            if (starterKit.value.is_owner) {
                await loadAccounts()
            }
        } else {
            await finishedLoading()
        }
    }
}

const loadUsed = async () => {
    try {
        const res = await api.get(`/api/v1/starter-kits/details/${kitId}/used`)
        const data = res.data.data
        if (data.limit_restriction) {
            limitRestriction.value = true
        } else {
            hasUsed.value = data.used === true
            delta.value = data.delta ?? []
        }
    } catch (e) {
        error.value = e?.response?.status === 404 ? 'not_found' : 'generic'
        loading.value = false
        return
    }
    await finishedLoading()
}

const finishedLoading = async () => {
    loading.value = false
}

const loadAccounts = async () => {
    try {
        const res = await api.get(`/api/v1/starter-kits/details/${kitId}/accounts`)
        starterKit.value.accounts = res.data.data
    } catch (e) {
        error.value = 'generic'
    } finally {
        loading.value = false
    }
}

const retryLoad = () => {
    loading.value = true
    error.value = false
    loadKit()
}

const handleProfileClick = (url) => {
    router.push('/@' + url)
}

const copyFederatedUrl = async () => {
    await navigator.clipboard.writeText(starterKit.value.url)
    copiedFederated.value = true
    setTimeout(() => {
        copiedFederated.value = false
    }, 2000)
}

const useStarterKit = async () => {
    if (!authStore.isAuthenticated) {
        showFederatedModal.value = true
        return
    }
    isUsing.value = true
    try {
        const api = axios.getAxiosInstance()
        await api.post(`/api/v1/starter-kits/details/${kitId}/use`)
        useSuccess.value = true
    } catch (err) {
        await alertModal(
            '⚠️ ' + t('common.somethingWentWrong'),
            err?.response?.data?.error?.message ||
                err?.response?.data?.message ||
                t('common.unexpectedError')
        )
    } finally {
        isUsing.value = false
    }
}

const reuseStarterKit = async () => {
    isReusingKit.value = true
    try {
        await api.post(`/api/v1/starter-kits/details/${kitId}/reuse`)
        reuseSuccess.value = true
        delta.value = []
        await loadUsed()
    } catch (err) {
        await alertModal(
            '⚠️ ' + t('common.somethingWentWrong'),
            err?.response?.data?.error?.message ||
                err?.response?.data?.message ||
                t('common.unexpectedError')
        )
    } finally {
        isReusingKit.value = false
    }
}

const copyShareUrl = async () => {
    await navigator.clipboard.writeText(starterKit.value.url)
    copied.value = true
    setTimeout(() => {
        copied.value = false
    }, 2000)
}

const handleSelfRemove = async () => {
    const result = await confirmModal(
        t('common.removeYourselfFromThisStarterKit') + '?',
        t('common.areYouSureYouWantToRemoveYourselfFromThisStarterKit') + '?',
        t('common.yesCommaRemoveMe'),
        t('common.cancel')
    )
    if (result) {
        try {
            await api.post(`/api/v1/starter-kits/details/${starterKit.value.id}/membership/revoke`)
        } catch (e) {
        } finally {
            loadKit()
        }
    }
}

const handleReport = async () => {
    if (!authStore.isAuthenticated) {
        authStore.openAuthModal('login', window.location.href)
        return
    }
    openReportModal('starter_kit', starterKit.value.id, window.location.href)
}

const editKit = () => {
    router.push(`${starterKit.value.path}/edit`)
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
</style>
