<template>
    <div class="space-y-6">
        <transition
            enter-active-class="transition-all duration-300 ease-out"
            leave-active-class="transition-all duration-200 ease-in"
            enter-from-class="opacity-0 -translate-y-2"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="
                    versionCheck &&
                    versionCheck.status === 'success' &&
                    versionCheck.update_available
                "
                class="bg-gradient-to-r from-indigo-500 to-blue-700 rounded-lg shadow-lg border border-blue-400/50 p-4"
            >
                <div class="flex items-center justify-between flex-col lg:flex-row gap-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg
                                class="w-6 h-6 text-white animate-pulse"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"
                                />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-1">Update Available! 🎉</h3>
                            <p class="text-blue-50 text-sm mb-2">
                                {{ versionCheck.release.name }}
                            </p>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-blue-100">
                                <span class="flex items-center gap-1">
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
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                        />
                                    </svg>
                                    Current:
                                    <span class="font-semibold">{{
                                        versionCheck.current_version
                                    }}</span>
                                </span>
                                <span class="text-blue-200">→</span>
                                <span class="flex items-center gap-1">
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
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                                        />
                                    </svg>
                                    Latest:
                                    <span class="font-semibold">{{
                                        versionCheck.latest_version
                                    }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a
                            :href="versionCheck.release.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors shadow-sm"
                        >
                            <span>View Release</span>
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
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                />
                            </svg>
                        </a>
                        <button
                            @click="dismissVersionCheck"
                            class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                            title="Dismiss"
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
                </div>
            </div>
        </transition>

        <transition
            enter-active-class="transition-all duration-300 ease-out"
            leave-active-class="transition-all duration-200 ease-in"
            enter-from-class="opacity-0 -translate-y-2"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="
                    versionCheck &&
                    versionCheck.status === 'failure' &&
                    versionCheck.error === 'prolonged_failure'
                "
                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4"
            >
                <div class="flex items-start gap-3">
                    <svg
                        class="w-5 h-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0 mt-0.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
                            Unable to Check for Updates
                        </h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            Version checks have been failing for
                            {{ versionCheck.failure_details.days_failing }} days. The update beacon
                            may be unreachable.
                        </p>
                    </div>
                    <button
                        @click="dismissVersionCheck"
                        class="p-1 text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200"
                        title="Dismiss"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </transition>

        <div class="flex items-center justify-center lg:justify-between flex-col xl:flex-row">
            <div class="flex justify-center items-center xl:items-start flex-col mb-3 xl:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400">Server overview and analytics.</p>
            </div>

            <div
                v-if="dashboardData?.cached_at"
                class="text-xs text-gray-400 mb-3 xl:mb-0 flex gap-1"
            >
                <span>Data Updated: {{ formatTimeAgo(dashboardData?.cached_at) }}</span>
            </div>

            <div class="flex flex-col lg:flex-row gap-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 lg:pr-4">
                        <span class="text-sm font-light text-gray-500">Select period: </span>
                        <div
                            class="flex bg-gray-100 dark:bg-neutral-800 border border-gray-200 dark:border-gray-700 shadow rounded-lg p-2 gap-1"
                        >
                            <button
                                v-for="filter in filterOptions"
                                :key="filter"
                                @click="activeFilter = filter"
                                :class="[
                                    'px-5 py-1.5 text-[10px] lg:text-[16px] font-medium rounded-md transition-all duration-200 relative cursor-pointer',
                                    activeFilter === filter
                                        ? 'bg-white dark:bg-neutral-700 text-black dark:text-white shadow-sm'
                                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:bg-gray-700 dark:hover:text-gray-200'
                                ]"
                            >
                                {{ filter }}
                            </button>
                        </div>
                    </div>
                </div>

                <AnimatedButton
                    type="button"
                    :loading="loading"
                    variant="primary"
                    @click="refreshDashboard"
                    title="Refresh stats"
                >
                    <div class="flex items-center font-bold gap-2">
                        <span class="bx bx-refresh text-[25px]"></span> Refresh
                    </div>
                </AnimatedButton>
            </div>
        </div>

        <div v-if="loading && !dashboardData" class="flex justify-center items-center py-20">
            <Spinner />
        </div>

        <div v-else class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <MetricCard
                    title="Total Users"
                    :value="dashboardData.metrics.total_users"
                    :change="dashboardData.metrics.users_change"
                    :icon="UserGroupIcon"
                    color="blue"
                />

                <MetricCard
                    title="Total Videos"
                    :value="dashboardData.metrics.total_videos"
                    :change="dashboardData.metrics.videos_change"
                    :icon="VideoCameraIcon"
                    color="yellow"
                />

                <MetricCard
                    title="Engagement Rate"
                    :value="`${dashboardData.metrics.engagement_rate}%`"
                    :change="dashboardData.metrics.engagement_change"
                    :icon="ChartBarIcon"
                    color="pink"
                />

                <MetricCard
                    title="Federated Instances"
                    :value="dashboardData.metrics.active_instances"
                    :change="dashboardData.metrics.instances_change"
                    :icon="GlobeAltIcon"
                    color="green"
                />
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <MiniMetric label="Active Today" :value="dashboardData.metrics.active_today" />
                <MiniMetric
                    label="Pending Reports"
                    :value="dashboardData.metrics.pending_reports"
                />
                <MiniMetric
                    label="Comments"
                    :value="formatCount(dashboardData.metrics.total_comments)"
                />
                <MiniMetric label="Likes" :value="formatCount(dashboardData.metrics.total_likes)" />
                <MiniMetric
                    label="Shares"
                    :value="formatCount(dashboardData.metrics.total_shares)"
                />
                <MiniMetric
                    label="Followers"
                    :value="formatCount(dashboardData.metrics.total_follows)"
                />
                <MiniMetric
                    label="Hashtags"
                    :value="formatCount(dashboardData.metrics.total_hashtags)"
                />
                <MiniMetric
                    v-if="dashboardData.metrics.for_you.total_views"
                    label="For You Total Views"
                    :value="formatCount(dashboardData.metrics.for_you.total_views)"
                />
                <MiniMetric
                    v-if="dashboardData.metrics.for_you.total_views"
                    label="For You Watch Time"
                    :value="dashboardData.metrics.for_you.total_watch_time"
                />
                <MiniMetric label="Storage Used" :value="dashboardData.metrics.storage_used" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <ChartCard :title="`Active Users (${dashboardData.period})`">
                    <div ref="activeUsersChart" class="h-80"></div>
                </ChartCard>

                <ChartCard :title="`User Growth (${dashboardData.period})`">
                    <div ref="userGrowthChart" class="h-80"></div>
                </ChartCard>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <ChartCard title="Engagement Metrics (7 Days)">
                    <div ref="engagementChart" class="h-120"></div>
                </ChartCard>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <ChartCard :title="`Video Uploads (${dashboardData.period})`">
                    <div ref="videoUploadsChart" class="h-80"></div>
                </ChartCard>

                <ChartCard title="Content Distribution">
                    <div ref="contentDistChart" class="h-80"></div>
                </ChartCard>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                >
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Trending Hashtags
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{
                            dashboardData.period
                        }}</span>
                    </div>

                    <div class="relative">
                        <div
                            ref="scrollContainer"
                            @scroll="handleScroll"
                            class="space-y-2 max-h-80 overflow-y-scroll overflow-x-visible pl-4 -ml-4 pr-2"
                        >
                            <div
                                v-for="(tag, index) in dashboardData.top_hashtags"
                                :key="tag.id"
                                class="group relative bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors"
                            >
                                <div class="absolute -left-2 top-1/2 -translate-y-1/2">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shadow-md"
                                        :class="{
                                            'bg-gradient-to-br from-yellow-400 to-yellow-600 text-yellow-900':
                                                index === 0,
                                            'bg-gradient-to-br from-gray-300 to-gray-400 text-gray-800':
                                                index === 1,
                                            'bg-gradient-to-br from-orange-400 to-orange-600 text-orange-900':
                                                index === 2,
                                            'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300':
                                                index > 2
                                        }"
                                    >
                                        {{ index + 1 }}
                                    </div>
                                </div>

                                <div class="ml-8">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <router-link
                                                :to="`/tag/${tag.name}`"
                                                class="text-lg font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors inline-flex items-center gap-2"
                                            >
                                                #{{ tag.name }}
                                                <span
                                                    v-if="index < 3"
                                                    title="This indicates a viral or popular hashtag"
                                                >
                                                    <svg
                                                        class="w-4 h-4 text-red-500 animate-pulse"
                                                        title="This indicates a viral or popular hashtag"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z"
                                                        />
                                                    </svg>
                                                </span>
                                            </router-link>
                                        </div>
                                        <router-link
                                            :to="`/admin/hashtags?id=${tag.id}`"
                                            class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity"
                                        >
                                            Manage →
                                        </router-link>
                                    </div>

                                    <div class="flex items-center gap-4 mb-2">
                                        <div
                                            :title="`Videos posted with this hashtag in the ${dashboardData.period}`"
                                            class="flex items-center gap-1.5 cursor-help"
                                        >
                                            <svg
                                                class="w-4 h-4 text-blue-500 dark:text-blue-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                                />
                                            </svg>
                                            <span
                                                class="text-sm font-semibold text-gray-900 dark:text-white"
                                            >
                                                {{ formatCount(tag.recent_video_count) }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                                >videos</span
                                            >
                                        </div>

                                        <div
                                            title="Total videos ever posted with this hashtag"
                                            class="flex items-center gap-1.5 cursor-help"
                                        >
                                            <svg
                                                class="w-4 h-4 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                                />
                                            </svg>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatCount(tag.count) }} total
                                            </span>
                                        </div>
                                    </div>

                                    <div class="relative">
                                        <div
                                            class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden"
                                        >
                                            <div
                                                class="h-full rounded-full transition-all duration-500"
                                                :class="{
                                                    'bg-gradient-to-r from-red-500 to-pink-500':
                                                        index === 0,
                                                    'bg-gradient-to-r from-orange-500 to-yellow-500':
                                                        index === 1,
                                                    'bg-gradient-to-r from-blue-500 to-cyan-500':
                                                        index === 2,
                                                    'bg-gradient-to-r from-purple-500 to-indigo-500':
                                                        index > 2
                                                }"
                                                :style="{
                                                    width: `${getTrendPercentage(tag.trend_score, dashboardData.top_hashtags[0].trend_score)}%`
                                                }"
                                            ></div>
                                        </div>
                                        <span
                                            title="Calculated from recent video count + engagement (likes, comments, shares, views)"
                                            class="absolute -top-5 right-0 text-xs font-medium text-gray-600 dark:text-gray-400 cursor-help"
                                        >
                                            {{ formatTrendScore(tag.trend_score) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <transition
                            enter-active-class="transition-opacity duration-300"
                            leave-active-class="transition-opacity duration-300"
                            enter-from-class="opacity-0"
                            leave-to-class="opacity-0"
                        >
                            <div
                                v-if="showScrollIndicator"
                                class="absolute bottom-0 -left-5 right-0 h-20 pointer-events-none bg-gradient-to-t from-white dark:from-gray-800 to-transparent flex items-end justify-center pb-3"
                            >
                                <div
                                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-full bg-gray-100 dark:bg-gray-700 shadow-lg"
                                >
                                    <svg
                                        class="w-4 h-4 text-gray-600 dark:text-gray-300 animate-bounce"
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
                                    <span
                                        class="text-xs font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Scroll for more
                                    </span>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Federation Overview
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Total Instances</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ dashboardData.federation.total_instances }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Remote Profiles</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ formatCount(dashboardData.federation.remote_profiles) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Remote Videos</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ formatCount(dashboardData.federation.remote_videos) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Blocked Instances</span>
                            <span class="text-xl font-bold text-red-600 dark:text-red-400">
                                {{ dashboardData.federation.blocked_instances }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Remote Comments</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ formatCount(dashboardData.federation.remote_comments) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Remote Replies</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ formatCount(dashboardData.federation.remote_replies) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Remote Followers</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ formatCount(dashboardData.federation.remote_followers) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Remote Following</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ formatCount(dashboardData.federation.remote_following) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Moderation Queue
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Pending Reports</span>
                            <span class="text-xl font-bold text-orange-600 dark:text-orange-400">
                                {{ dashboardData.moderation.pending_reports }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Suspended Users</span>
                            <span class="text-xl font-bold text-red-600 dark:text-red-400">
                                {{ dashboardData.moderation.suspended_users }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Total Reports (30d)</span
                            >
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ dashboardData.moderation.total_reports_30d }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Recent Activity
                </h3>
                <div class="space-y-3">
                    <div
                        v-for="activity in dashboardData.recent_activity"
                        :key="activity.id"
                        class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="pr-3 text-gray-300 dark:text-gray-600">
                                <div v-if="activity.icon === 'new_user'">
                                    <UserIcon class="w-8 h-8" />
                                </div>
                                <div v-else-if="activity.icon === 'new_video'">
                                    <VideoCameraIcon class="w-8 h-8" />
                                </div>
                                <div v-else-if="activity.icon === 'new_report'">
                                    <ExclamationTriangleIcon class="w-8 h-8" />
                                </div>
                            </div>
                            <div>
                                <p class="text-gray-900 dark:text-white">
                                    {{ activity.description }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDate(activity.timestamp) }}
                                </p>
                            </div>
                        </div>
                        <router-link
                            v-if="activity.link"
                            :to="activity.link"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                        >
                            View →
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ChartBarSquareIcon,
    Cog6ToothIcon,
    ChatBubbleOvalLeftIcon,
    ChatBubbleLeftRightIcon,
    ChartBarIcon,
    HashtagIcon,
    GlobeAltIcon,
    ExclamationTriangleIcon,
    UserGroupIcon,
    UserIcon,
    VideoCameraIcon,
    ServerStackIcon,
    HomeIcon,
    SunIcon,
    MoonIcon
} from '@heroicons/vue/24/outline'

import { ref, onMounted, onUnmounted, nextTick, watch, computed } from 'vue'
import * as echarts from 'echarts'
import { storeToRefs } from 'pinia'
import { dashboardApi } from '@/services/adminApi'
import { useAlertModal } from '@/composables/useAlertModal.js'
import { useUtils } from '@/composables/useUtils'
import { useAdminStore } from '~/stores/admin'
import AnimatedButton from '@/components/AnimatedButton.vue'

const { formatDate, formatCount, formatTimeAgo } = useUtils()
const { alertModal } = useAlertModal()
const adminStore = useAdminStore()
const { isDarkMode } = storeToRefs(adminStore)

const loading = ref(true)
const dashboardData = ref(null)
const shouldRefresh = ref(false)
const scrollContainer = ref(null)
const showScrollIndicator = ref(false)
const versionCheck = ref(null)
const versionCheckDismissed = ref(false)

const activeFilter = ref('30d')

const filterOptions = ['30d', '60d', '90d', '365d']

const activeUsersChart = ref(null)
const userGrowthChart = ref(null)
const videoUploadsChart = ref(null)
const engagementChart = ref(null)
const contentDistChart = ref(null)

let activeUsersInstance = null
let userGrowthInstance = null
let videoUploadsInstance = null
let engagementInstance = null
let contentDistInstance = null

const axisLineColor = computed(() => {
    return isDarkMode.value ? '#333' : '#ccc'
})

const fetchDashboardData = async () => {
    loading.value = true
    try {
        const response = await dashboardApi.getDashboardStats(
            activeFilter.value,
            shouldRefresh.value
        )
        dashboardData.value = response.data

        await nextTick()
        shouldRefresh.value = false
        initializeCharts()
        tagScrollToTop()
    } catch (error) {
        alertModal('Error fetching dashboard data', error.message)
    } finally {
        loading.value = false
    }
}

const initializeCharts = () => {
    if (!dashboardData.value) return
    ;[
        userGrowthInstance,
        videoUploadsInstance,
        engagementInstance,
        contentDistInstance,
        activeUsersInstance
    ].forEach((instance) => instance?.dispose())

    initUserGrowthChart()
    initActiveUsersChart()
    initVideoUploadsChart()
    initEngagementChart()
    initContentDistChart()
    checkScrollIndicator()
    tagScrollToTop()
}

const fetchVersionCheck = async () => {
    try {
        const response = await dashboardApi.getVersionCheck()
        versionCheck.value = response
    } catch (error) {
        console.warn('Version check failed:', error)
    }
}

const dismissVersionCheck = () => {
    versionCheckDismissed.value = true
    versionCheck.value = null
}

const initActiveUsersChart = () => {
    if (!activeUsersChart.value) return

    activeUsersInstance = echarts.init(activeUsersChart.value, isDarkMode.value ? 'dark' : 'light')
    const data = dashboardData.value.charts.active_users

    const option = {
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            borderColor: '#333',
            textStyle: { color: '#fff' }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: data.dates,
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' }
        },
        yAxis: {
            type: 'value',
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' },
            splitLine: { lineStyle: { color: axisLineColor.value } }
        },
        series: [
            {
                name: 'Active Users',
                type: 'line',
                smooth: true,
                data: data.values,
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(59, 130, 246, 0.5)' },
                        { offset: 1, color: 'rgba(59, 130, 246, 0.1)' }
                    ])
                },
                lineStyle: { color: '#3b82f6', width: 2 },
                itemStyle: { color: '#3b82f6' }
            }
        ]
    }

    activeUsersInstance.setOption(option)
}

const initUserGrowthChart = () => {
    if (!userGrowthChart.value) return

    userGrowthInstance = echarts.init(userGrowthChart.value, isDarkMode.value ? 'dark' : 'light')
    const data = dashboardData.value.charts.user_growth

    const option = {
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            borderColor: '#333',
            textStyle: { color: '#fff' }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: data.dates,
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' }
        },
        yAxis: {
            type: 'value',
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' },
            splitLine: { lineStyle: { color: axisLineColor.value } }
        },
        series: [
            {
                name: 'New Users',
                type: 'line',
                smooth: true,
                data: data.values,
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(59, 130, 246, 0.5)' },
                        { offset: 1, color: 'rgba(59, 130, 246, 0.1)' }
                    ])
                },
                lineStyle: { color: '#3b82f6', width: 2 },
                itemStyle: { color: '#3b82f6' }
            }
        ]
    }

    userGrowthInstance.setOption(option)
}

const initVideoUploadsChart = () => {
    if (!videoUploadsChart.value) return

    videoUploadsInstance = echarts.init(
        videoUploadsChart.value,
        isDarkMode.value ? 'dark' : 'light'
    )
    const data = dashboardData.value.charts.video_uploads

    const option = {
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            borderColor: '#333',
            textStyle: { color: '#fff' }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: data.dates,
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' }
        },
        yAxis: {
            type: 'value',
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' },
            splitLine: { lineStyle: { color: axisLineColor.value } }
        },
        series: [
            {
                name: 'Videos',
                type: 'bar',
                data: data.values,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#a855f7' },
                        { offset: 1, color: '#7c3aed' }
                    ])
                }
            }
        ]
    }

    videoUploadsInstance.setOption(option)
}

const initEngagementChart = () => {
    if (!engagementChart.value) return

    engagementInstance = echarts.init(engagementChart.value, isDarkMode.value ? 'dark' : 'light')
    const data = dashboardData.value.charts.engagement

    const option = {
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            borderColor: '#333',
            textStyle: { color: '#fff' }
        },
        legend: {
            data: [
                'Likes',
                'Comments',
                'Replies',
                'Reports',
                'Bookmarks',
                'Shares',
                'Comment Likes',
                'Reply Likes',
                'Comment Shares',
                'Reply Shares'
            ],
            textStyle: { color: '#999' },
            bottom: '0%',
            type: 'scroll'
        },
        emphasis: {
            focus: 'series',
            lineStyle: { width: 5 }
        },
        grid: {
            top: '5%',
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: data.dates,
            boundaryGap: false,
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' }
        },
        yAxis: {
            min:
                Math.min(
                    ...Object.values(data)
                        .flat()
                        .filter((v) => v > 0)
                ) || 1,
            type: 'log',
            logBase: 10,
            axisLine: { lineStyle: { color: axisLineColor.value } },
            axisLabel: { color: '#999' },
            splitLine: { lineStyle: { color: axisLineColor.value } }
        },
        series: [
            {
                name: 'Likes',
                type: 'line',
                smooth: true,
                data: data.likes,
                lineStyle: { color: '#ec4899', width: 5 },
                itemStyle: { color: '#ec4899' }
            },
            {
                name: 'Comments',
                type: 'line',
                smooth: true,
                data: data.comments,
                lineStyle: { color: '#3b82f6', width: 4 },
                itemStyle: { color: '#3b82f6' }
            },
            {
                name: 'Replies',
                type: 'line',
                smooth: true,
                data: data.replies,
                lineStyle: { color: '#f59e0b', width: 4 },
                itemStyle: { color: '#f59e0b' }
            },
            {
                name: 'Reports',
                type: 'line',
                smooth: false,
                data: data.reports,
                lineStyle: { color: '#f43f5e', width: 4 },
                itemStyle: { color: '#f43f5e' }
            },
            {
                name: 'Bookmarks',
                type: 'line',
                smooth: true,
                data: data.bookmarks,
                lineStyle: { color: '#a855f7', width: 4 },
                itemStyle: { color: '#a855f7' }
            },
            {
                name: 'Shares',
                type: 'line',
                smooth: false,
                data: data.shares,
                lineStyle: { color: '#10b981', type: 'dashed' },
                itemStyle: { color: '#10b981' }
            },
            {
                name: 'Comment Likes',
                type: 'line',
                smooth: false,
                data: data.commentLikes,
                lineStyle: { color: '#8b5cf6', type: 'dashed' },
                itemStyle: { color: '#8b5cf6' }
            },
            {
                name: 'Reply Likes',
                type: 'line',
                smooth: false,
                data: data.replyLikes,
                lineStyle: { color: '#ef4444', type: 'dashed' },
                itemStyle: { color: '#ef4444' }
            },
            {
                name: 'Comment Shares',
                type: 'line',
                smooth: false,
                data: data.commentShares,
                lineStyle: { color: '#06b6d4', type: 'dotted' },
                itemStyle: { color: '#06b6d4' }
            },
            {
                name: 'Reply Shares',
                type: 'line',
                smooth: false,
                data: data.replyShares,
                lineStyle: { color: '#eab308', type: 'dotted' },
                itemStyle: { color: '#eab308' }
            }
        ]
    }

    engagementInstance.setOption(option)
}

const initContentDistChart = () => {
    if (!contentDistChart.value) return

    contentDistInstance = echarts.init(contentDistChart.value, isDarkMode.value ? 'dark' : 'light')
    const data = dashboardData.value.charts.content_distribution

    const option = {
        tooltip: {
            trigger: 'item',
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            borderColor: '#333',
            textStyle: { color: '#fff' }
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            textStyle: { color: '#999' }
        },
        series: [
            {
                name: 'Content',
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: isDarkMode.value ? '#1f2937' : '#fff',
                    borderWidth: 2
                },
                label: {
                    show: false,
                    position: 'center'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 20,
                        fontWeight: 'bold',
                        color: isDarkMode.value ? '#fff' : '#000'
                    }
                },
                labelLine: {
                    show: false
                },
                data: data
            }
        ]
    }

    contentDistInstance.setOption(option)
}

const refreshDashboard = async () => {
    shouldRefresh.value = true
    await nextTick()
    fetchDashboardData()
}

const formatTrendScore = (score) => {
    if (score >= 1000000) {
        return (score / 1000000).toFixed(1) + 'M'
    }
    if (score >= 1000) {
        return (score / 1000).toFixed(0) + 'K'
    }
    return Math.round(score).toString()
}

const handleScroll = (event) => {
    const container = event.target
    const scrollTop = container.scrollTop
    const scrollThreshold = 50

    showScrollIndicator.value =
        scrollTop < scrollThreshold && dashboardData.value.top_hashtags.length > 5
}

const checkScrollIndicator = () => {
    if (scrollContainer.value && dashboardData.value.top_hashtags.length > 5) {
        const container = scrollContainer.value
        const hasScroll = container.scrollHeight > container.clientHeight
        showScrollIndicator.value = hasScroll && container.scrollTop < 50
    }
}

const tagScrollToTop = () => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollTo({
            top: 0,
            behavior: 'smooth'
        })
    }
}

const getTrendPercentage = (score, maxScore) => {
    return Math.min(100, (score / maxScore) * 100)
}

const handleResize = () => {
    ;[userGrowthInstance, videoUploadsInstance, engagementInstance, contentDistInstance].forEach(
        (instance) => instance?.resize()
    )
}

watch(activeFilter, (newVal, oldVal) => {
    fetchDashboardData()
})

watch(isDarkMode, (newVal, oldVal) => {
    fetchDashboardData()
})

onMounted(async () => {
    checkScrollIndicator()
    fetchDashboardData()
    fetchVersionCheck()
    window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
    ;[userGrowthInstance, videoUploadsInstance, engagementInstance, contentDistInstance].forEach(
        (instance) => instance?.dispose()
    )
    window.removeEventListener('resize', handleResize)
})
</script>
