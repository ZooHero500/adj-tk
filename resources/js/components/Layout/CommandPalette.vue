<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 dark:bg-black/60 backdrop-blur-sm"
                @click="close"
            >
                <div class="min-h-screen px-4 flex items-start justify-center pt-[15vh]">
                    <Transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-4"
                    >
                        <div
                            v-if="isOpen"
                            @click.stop
                            class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10"
                        >
                            <div
                                class="relative flex items-center px-4 border-b border-gray-200 dark:border-gray-700"
                            >
                                <MagnifyingGlassIcon
                                    class="h-5 w-5 text-gray-400 dark:text-gray-500 flex-shrink-0"
                                />
                                <input
                                    ref="searchInput"
                                    v-model="query"
                                    type="text"
                                    :placeholder="activePlaceholder"
                                    class="w-full px-3 py-4 bg-transparent text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none text-base"
                                    @keydown.down.prevent="
                                        selectedIndex = Math.min(
                                            selectedIndex + 1,
                                            flatResults.length - 1
                                        )
                                    "
                                    @keydown.up.prevent="
                                        selectedIndex = Math.max(selectedIndex - 1, 0)
                                    "
                                    @keydown.enter.prevent="
                                        executeCommand(flatResults[selectedIndex])
                                    "
                                    @keydown.esc="close"
                                    @keydown.tab.prevent="applyAutoComplete"
                                />

                                <Transition
                                    enter-active-class="transition-all duration-150 ease-out"
                                    enter-from-class="opacity-0 scale-90"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition-all duration-100 ease-in"
                                    leave-from-class="opacity-100 scale-100"
                                    leave-to-class="opacity-0 scale-90"
                                >
                                    <span
                                        v-if="activePrefix"
                                        class="flex-shrink-0 px-2 py-1 text-xs font-bold rounded-md bg-primary/10 text-primary border border-primary/20"
                                    >
                                        {{ activePrefixLabel }}
                                    </span>
                                </Transition>

                                <div class="flex items-center gap-1.5 ml-2 flex-shrink-0">
                                    <kbd
                                        class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600"
                                    >
                                        ↑↓
                                    </kbd>
                                    <kbd
                                        class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600"
                                    >
                                        ↵
                                    </kbd>
                                    <kbd
                                        class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600"
                                    >
                                        tab
                                    </kbd>
                                </div>
                            </div>

                            <div
                                v-if="flatResults.length > 0"
                                class="max-h-[400px] overflow-y-auto py-2 px-2"
                            >
                                <template
                                    v-for="(group, groupIndex) in groupedResults"
                                    :key="group.label"
                                >
                                    <div
                                        v-if="group.label"
                                        class="px-3 pt-3 pb-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 select-none"
                                        :class="{ 'pt-1.5': groupIndex === 0 }"
                                    >
                                        {{ group.label }}
                                    </div>

                                    <div
                                        v-for="command in group.items"
                                        :key="command.id"
                                        @click="executeCommand(command)"
                                        @mouseenter="selectedIndex = flatResults.indexOf(command)"
                                        :class="[
                                            'group flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-colors',
                                            flatResults.indexOf(command) === selectedIndex
                                                ? 'bg-gray-500/5 dark:bg-gray-500/10'
                                                : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'
                                        ]"
                                    >
                                        <div
                                            :class="[
                                                'flex items-center justify-center w-9 h-9 rounded-lg transition-colors flex-shrink-0',
                                                flatResults.indexOf(command) === selectedIndex
                                                    ? command.iconBg
                                                    : 'bg-gray-100 dark:bg-gray-700'
                                            ]"
                                        >
                                            <component
                                                :is="command.icon"
                                                :class="[
                                                    'w-5 h-5 transition-colors',
                                                    flatResults.indexOf(command) === selectedIndex
                                                        ? command.iconColor
                                                        : 'text-gray-500 dark:text-gray-400'
                                                ]"
                                            />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                                                v-html="
                                                    highlightMatch(
                                                        command.title,
                                                        searchTermForHighlight
                                                    )
                                                "
                                            ></div>
                                            <div
                                                v-if="command.description"
                                                class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                            >
                                                {{ command.description }}
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span
                                                v-if="command.badge"
                                                class="px-2 py-0.5 text-[11px] font-semibold rounded-md"
                                                :class="
                                                    command.badgeClass ||
                                                    'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                                                "
                                            >
                                                {{ command.badge }}
                                            </span>
                                            <svg
                                                v-if="
                                                    flatResults.indexOf(command) === selectedIndex
                                                "
                                                class="w-4 h-4 text-primary"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div
                                v-else
                                class="py-12 px-4 text-center text-gray-500 dark:text-gray-400"
                            >
                                <MagnifyingGlassIcon
                                    class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3"
                                />
                                <p class="text-sm font-medium">No results found</p>
                                <p class="text-xs mt-1">Try a different search term or prefix</p>
                            </div>

                            <div
                                class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-4 py-2.5"
                            >
                                <div
                                    class="flex items-center gap-3 text-[11px] text-gray-400 dark:text-gray-500 overflow-x-auto"
                                >
                                    <template v-if="!query">
                                        <span
                                            v-for="hint in defaultHints"
                                            :key="hint.prefix"
                                            class="flex items-center gap-1.5 flex-shrink-0 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                            @click="applyPrefix(hint.prefix)"
                                        >
                                            <kbd
                                                class="px-1.5 py-0.5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 font-mono font-semibold text-gray-500 dark:text-gray-400"
                                            >
                                                {{ hint.prefix }}
                                            </kbd>
                                            <span>{{ hint.label }}</span>
                                        </span>
                                    </template>
                                    <template v-else-if="activePrefix">
                                        <span class="text-primary font-medium">{{
                                            contextualHint
                                        }}</span>
                                    </template>
                                    <template v-else>
                                        <span class="flex items-center gap-1.5">
                                            <kbd
                                                class="px-1.5 py-0.5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 font-mono font-semibold"
                                                >Tab</kbd
                                            >
                                            Autocomplete
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <kbd
                                                class="px-1.5 py-0.5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 font-mono font-semibold"
                                                >Esc</kbd
                                            >
                                            Close
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import {
    MagnifyingGlassIcon,
    ChartBarSquareIcon,
    ChatBubbleOvalLeftIcon,
    ChatBubbleLeftRightIcon,
    HashtagIcon,
    ExclamationTriangleIcon,
    UserGroupIcon,
    VideoCameraIcon,
    ServerStackIcon,
    Cog6ToothIcon,
    ArrowPathIcon,
    CommandLineIcon,
    ShieldCheckIcon,
    DocumentMagnifyingGlassIcon,
    FunnelIcon,
    StarIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const isOpen = ref(false)
const query = ref('')
const selectedIndex = ref(0)
const searchInput = ref(null)

const prefixes = {
    'u:': {
        label: 'User Search',
        placeholder: 'Search by username...',
        hint: 'Type a username to find profiles — prefix with @ or without',
        resolve: (q) => {
            const clean = q.replace(/^@/, '').trim()
            if (!clean) return []
            return [
                {
                    id: `user-search-${clean}`,
                    title: `Search profiles for "${clean}"`,
                    description: 'Jump to profile search results',
                    icon: UserGroupIcon,
                    iconBg: 'bg-amber-100 dark:bg-amber-900/50',
                    iconColor: 'text-amber-600 dark:text-amber-400',
                    action: () => router.push(`/admin/profiles?q=${encodeURIComponent(clean)}`),
                    badge: 'Profile',
                    badgeClass:
                        'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400'
                }
            ]
        }
    },
    'v:': {
        label: 'Video Search',
        placeholder: 'Search videos...',
        hint: 'Search video titles, descriptions, and metadata',
        resolve: (q) => {
            const clean = q.trim()
            if (!clean) return []
            return [
                {
                    id: `video-search-${clean}`,
                    title: `Search videos for "${clean}"`,
                    description: 'Jump to video search results',
                    icon: VideoCameraIcon,
                    iconBg: 'bg-primary/10 dark:bg-primary/20',
                    iconColor: 'text-primary',
                    action: () => router.push(`/admin/videos?q=${encodeURIComponent(clean)}`),
                    badge: 'Video',
                    badgeClass: 'bg-primary/10 text-primary'
                }
            ]
        }
    },
    '#': {
        label: 'Hashtag Search',
        placeholder: 'Search hashtags...',
        hint: 'Find hashtags by name — results include trending status',
        resolve: (q) => {
            const clean = q.replace(/^#/, '').trim()
            if (!clean) return []
            return [
                {
                    id: `hashtag-search-${clean}`,
                    title: `Search hashtags for "#${clean}"`,
                    description: 'Jump to hashtag search results',
                    icon: HashtagIcon,
                    iconBg: 'bg-purple-100 dark:bg-purple-900/50',
                    iconColor: 'text-purple-600 dark:text-purple-400',
                    action: () => router.push(`/admin/hashtags?q=${encodeURIComponent(clean)}`),
                    badge: 'Hashtag',
                    badgeClass:
                        'bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400'
                }
            ]
        }
    },
    'i:': {
        label: 'Instance Search',
        placeholder: 'Search federated instances...',
        hint: 'Search by domain — e.g. mastodon.social, pixelfed.social',
        resolve: (q) => {
            const clean = q.trim()
            if (!clean) return []
            return [
                {
                    id: `instance-search-${clean}`,
                    title: `Search instances for "${clean}"`,
                    description: 'Jump to instance search results',
                    icon: ServerStackIcon,
                    iconBg: 'bg-cyan-100 dark:bg-cyan-900/50',
                    iconColor: 'text-cyan-600 dark:text-cyan-400',
                    action: () => router.push(`/admin/instances?q=${encodeURIComponent(clean)}`),
                    badge: 'Instance',
                    badgeClass: 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400'
                }
            ]
        }
    },
    'q:': {
        label: 'Full Text Search',
        placeholder: 'Search everything...',
        hint: 'Search across profiles, videos, hashtags, and instances',
        resolve: (q) => {
            const clean = q.trim()
            if (!clean) return []
            return [
                {
                    id: `fts-profiles-${clean}`,
                    title: `Profiles matching "${clean}"`,
                    description: 'Search user profiles',
                    icon: UserGroupIcon,
                    iconBg: 'bg-amber-100 dark:bg-amber-900/50',
                    iconColor: 'text-amber-600 dark:text-amber-400',
                    action: () => router.push(`/admin/profiles?q=${encodeURIComponent(clean)}`),
                    badge: 'Profile',
                    badgeClass:
                        'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400'
                },
                {
                    id: `fts-videos-${clean}`,
                    title: `Videos matching "${clean}"`,
                    description: 'Search video content',
                    icon: VideoCameraIcon,
                    iconBg: 'bg-primary/10 dark:bg-primary/20',
                    iconColor: 'text-primary',
                    action: () => router.push(`/admin/videos?q=${encodeURIComponent(clean)}`),
                    badge: 'Video',
                    badgeClass: 'bg-primary/10 text-primary'
                },
                {
                    id: `fts-hashtags-${clean}`,
                    title: `Hashtags matching "${clean}"`,
                    description: 'Search hashtags',
                    icon: HashtagIcon,
                    iconBg: 'bg-purple-100 dark:bg-purple-900/50',
                    iconColor: 'text-purple-600 dark:text-purple-400',
                    action: () => router.push(`/admin/hashtags?q=${encodeURIComponent(clean)}`),
                    badge: 'Hashtag',
                    badgeClass:
                        'bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400'
                },
                {
                    id: `fts-instances-${clean}`,
                    title: `Instances matching "${clean}"`,
                    description: 'Search federated instances',
                    icon: ServerStackIcon,
                    iconBg: 'bg-cyan-100 dark:bg-cyan-900/50',
                    iconColor: 'text-cyan-600 dark:text-cyan-400',
                    action: () => router.push(`/admin/instances?q=${encodeURIComponent(clean)}`),
                    badge: 'Instance',
                    badgeClass: 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400'
                }
            ]
        }
    }
}

const slashCommands = [
    {
        id: 'cmd-mod',
        title: '/mod',
        description: 'Jump to moderation queue',
        icon: ShieldCheckIcon,
        iconBg: 'bg-primary/10 dark:bg-primary/20',
        iconColor: 'text-primary',
        action: () => router.push('/admin/reports'),
        badge: 'Command',
        badgeClass: 'bg-primary/10 text-primary',
        keywords: ['moderation', 'reports', 'abuse', 'flags']
    },
    {
        id: 'cmd-list',
        title: '/list',
        description: 'List all admin pages',
        icon: CommandLineIcon,
        iconBg: 'bg-primary/10 dark:bg-primary/20',
        iconColor: 'text-primary',
        action: () => {},
        badge: 'Command',
        badgeClass: 'bg-primary/10 text-primary',
        keywords: ['all', 'pages', 'commands'],
        expandsAll: true
    },
    {
        id: 'cmd-settings',
        title: '/settings',
        description: 'Open admin settings',
        icon: Cog6ToothIcon,
        iconBg: 'bg-primary/10 dark:bg-primary/20',
        iconColor: 'text-primary',
        action: () => router.push('/admin/settings'),
        badge: 'Command',
        badgeClass: 'bg-primary/10 text-primary',
        keywords: ['config', 'configuration', 'preferences']
    },
    {
        id: 'cmd-federation',
        title: '/federation',
        description: 'View federation overview',
        icon: ServerStackIcon,
        iconBg: 'bg-primary/10 dark:bg-primary/20',
        iconColor: 'text-primary',
        action: () => router.push('/admin/instances'),
        badge: 'Command',
        badgeClass: 'bg-primary/10 text-primary',
        keywords: ['instances', 'fediverse', 'activitypub']
    },
    {
        id: 'cmd-starter-kits',
        title: '/starterkits',
        description: 'Manage starter kits',
        icon: StarIcon,
        iconBg: 'bg-primary/10 dark:bg-primary/20',
        iconColor: 'text-primary',
        action: () => router.push('/admin/starterkits'),
        badge: 'Command',
        badgeClass: 'bg-primary/10 text-primary',
        keywords: ['kits', 'curated', 'onboarding']
    }
]

const navCommands = [
    {
        id: 'dashboard',
        title: 'Dashboard',
        description: 'View admin dashboard',
        icon: ChartBarSquareIcon,
        iconBg: 'bg-blue-100 dark:bg-blue-900/50',
        iconColor: 'text-blue-600 dark:text-blue-400',
        action: () => router.push('/admin/dashboard'),
        keywords: ['home', 'overview', 'stats'],
        group: 'Navigation'
    },
    {
        id: 'comments',
        title: 'Comments',
        description: 'Manage comments',
        icon: ChatBubbleOvalLeftIcon,
        iconBg: 'bg-green-100 dark:bg-green-900/50',
        iconColor: 'text-green-600 dark:text-green-400',
        action: () => router.push('/admin/comments'),
        keywords: ['moderate', 'moderation'],
        group: 'Navigation'
    },
    {
        id: 'replies',
        title: 'Replies',
        description: 'Manage replies',
        icon: ChatBubbleLeftRightIcon,
        iconBg: 'bg-teal-100 dark:bg-teal-900/50',
        iconColor: 'text-teal-600 dark:text-teal-400',
        action: () => router.push('/admin/replies'),
        keywords: ['responses', 'threads'],
        group: 'Navigation'
    },
    {
        id: 'hashtags',
        title: 'Hashtags',
        description: 'Manage hashtags',
        icon: HashtagIcon,
        iconBg: 'bg-purple-100 dark:bg-purple-900/50',
        iconColor: 'text-purple-600 dark:text-purple-400',
        action: () => router.push('/admin/hashtags'),
        keywords: ['tags', 'trending'],
        group: 'Navigation'
    },
    {
        id: 'instances',
        title: 'Instances',
        description: 'Manage federated instances',
        icon: ServerStackIcon,
        iconBg: 'bg-cyan-100 dark:bg-cyan-900/50',
        iconColor: 'text-cyan-600 dark:text-cyan-400',
        action: () => router.push('/admin/instances'),
        keywords: ['federation', 'servers', 'fediverse'],
        group: 'Navigation'
    },
    {
        id: 'relays',
        title: 'Relays',
        description: 'Manage ActivityPub relays',
        icon: ArrowPathIcon,
        iconBg: 'bg-cyan-100 dark:bg-cyan-900/50',
        iconColor: 'text-cyan-600 dark:text-cyan-400',
        action: () => router.push('/admin/relays'),
        keywords: ['activitypub', 'sync'],
        group: 'Navigation'
    },
    {
        id: 'reports',
        title: 'Reports',
        description: 'View and handle reports',
        icon: ExclamationTriangleIcon,
        iconBg: 'bg-red-100 dark:bg-red-900/50',
        iconColor: 'text-red-600 dark:text-red-400',
        action: () => router.push('/admin/reports'),
        keywords: ['flags', 'moderation', 'abuse'],
        group: 'Navigation'
    },
    {
        id: 'profiles',
        title: 'Profiles',
        description: 'Manage user profiles',
        icon: UserGroupIcon,
        iconBg: 'bg-amber-100 dark:bg-amber-900/50',
        iconColor: 'text-amber-600 dark:text-amber-400',
        action: () => router.push('/admin/profiles'),
        keywords: ['users', 'accounts'],
        group: 'Navigation'
    },
    {
        id: 'videos',
        title: 'Videos',
        description: 'Manage videos',
        icon: VideoCameraIcon,
        iconBg: 'bg-primary/10 dark:bg-primary/20',
        iconColor: 'text-primary',
        action: () => router.push('/admin/videos'),
        keywords: ['content', 'media', 'loops'],
        group: 'Navigation'
    },
    {
        id: 'starter-kits',
        title: 'Starter Kits',
        description: 'Manage starter kits',
        icon: StarIcon,
        iconBg: 'bg-orange-100 dark:bg-orange-900/50',
        iconColor: 'text-orange-600 dark:text-orange-400',
        action: () => router.push('/admin/starterkits'),
        keywords: ['curated', 'onboarding', 'kits'],
        group: 'Navigation'
    },
    {
        id: 'settings',
        title: 'Settings',
        description: 'Admin settings',
        icon: Cog6ToothIcon,
        iconBg: 'bg-gray-200 dark:bg-gray-700',
        iconColor: 'text-gray-600 dark:text-gray-400',
        action: () => router.push('/admin/settings'),
        keywords: ['config', 'configuration', 'preferences'],
        group: 'Navigation'
    }
]

const defaultHints = [
    { prefix: '/', label: 'Commands' },
    { prefix: 'u:', label: 'Users' },
    { prefix: 'v:', label: 'Videos' },
    { prefix: '#', label: 'Hashtags' },
    { prefix: 'i:', label: 'Instances' },
    { prefix: 'q:', label: 'Search all' }
]

const activePrefix = computed(() => {
    const q = query.value
    if (q.startsWith('/')) return '/'
    for (const prefix of Object.keys(prefixes)) {
        if (q.startsWith(prefix)) return prefix
    }
    return null
})

const activePrefixLabel = computed(() => {
    if (!activePrefix.value) return ''
    if (activePrefix.value === '/') return 'Command'
    return prefixes[activePrefix.value]?.label || ''
})

const activePlaceholder = computed(() => {
    if (activePrefix.value && activePrefix.value !== '/' && prefixes[activePrefix.value]) {
        return prefixes[activePrefix.value].placeholder
    }
    return 'Search or type a command...'
})

const contextualHint = computed(() => {
    if (activePrefix.value === '/') return 'Type a command name — try /mod, /settings, /list'
    if (activePrefix.value && prefixes[activePrefix.value]) {
        return prefixes[activePrefix.value].hint
    }
    return ''
})

const searchTermForHighlight = computed(() => {
    const q = query.value
    if (activePrefix.value && activePrefix.value !== '/') {
        return q.slice(activePrefix.value.length).trim()
    }
    return q
})

const groupedResults = computed(() => {
    const q = query.value.trim().toLowerCase()

    if (!q) {
        return [
            {
                label: 'Quick Actions',
                items: navCommands.slice(0, 6)
            }
        ]
    }

    if (q.startsWith('/')) {
        const slashQ = q.slice(1)

        const listCmd = slashCommands.find((c) => c.expandsAll)
        if (slashQ === 'list' || slashQ === 'list ') {
            return [
                {
                    label: 'Commands',
                    items: slashCommands.filter((c) => !c.expandsAll)
                },
                {
                    label: 'All Pages',
                    items: navCommands
                }
            ]
        }

        const matchedSlash = slashCommands.filter((c) => {
            const name = c.title.toLowerCase()
            const kw = c.keywords || []
            return name.includes(q) || kw.some((k) => k.includes(slashQ))
        })

        return matchedSlash.length ? [{ label: 'Commands', items: matchedSlash }] : []
    }

    if (activePrefix.value && prefixes[activePrefix.value]) {
        const prefixDef = prefixes[activePrefix.value]
        const searchQ = q.slice(activePrefix.value.length)
        const results = prefixDef.resolve(searchQ)
        return results.length ? [{ label: prefixDef.label, items: results }] : []
    }

    const scored = navCommands
        .map((command) => {
            const term = q
            const title = command.title.toLowerCase()
            const desc = (command.description || '').toLowerCase()
            const kws = command.keywords || []

            let score = 0
            if (title === term) score = 100
            else if (title.startsWith(term)) score = 80
            else if (title.includes(term)) score = 60
            else if (desc.includes(term)) score = 40
            else if (kws.some((k) => k.includes(term))) score = 30
            else if (fuzzyMatch(term, title)) score = 20

            return { command, score }
        })
        .filter((s) => s.score > 0)
        .sort((a, b) => b.score - a.score)

    const matched = scored.map((s) => s.command)

    return matched.length ? [{ label: 'Results', items: matched }] : []
})

const flatResults = computed(() => {
    return groupedResults.value.flatMap((g) => g.items)
})

function fuzzyMatch(pattern, text) {
    let pi = 0
    for (let ti = 0; ti < text.length && pi < pattern.length; ti++) {
        if (text[ti] === pattern[pi]) pi++
    }
    return pi === pattern.length
}

function highlightMatch(text, search) {
    if (!search) return text
    const escaped = search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
    const regex = new RegExp(`(${escaped})`, 'gi')
    return text.replace(
        regex,
        '<mark class="bg-primary/15 text-primary dark:text-[#ff6b8a] font-semibold rounded-sm px-0.5">$1</mark>'
    )
}

function applyPrefix(prefix) {
    query.value = prefix
    nextTick(() => searchInput.value?.focus())
}

function applyAutoComplete() {
    if (flatResults.value.length > 0 && selectedIndex.value >= 0) {
        const selected = flatResults.value[selectedIndex.value]
        if (selected?.title?.startsWith('/')) {
            query.value = selected.title + ' '
            return
        }
    }
    const q = query.value.toLowerCase()
    if (q === 'u' || q === 'user') {
        query.value = 'u:'
        return
    }
    if (q === 'v' || q === 'vid') {
        query.value = 'v:'
        return
    }
    if (q === 'i' || q === 'inst') {
        query.value = 'i:'
        return
    }
    if (q === 'q' || q === 'search') {
        query.value = 'q:'
        return
    }
}

const open = () => {
    isOpen.value = true
    nextTick(() => searchInput.value?.focus())
}

const close = () => {
    isOpen.value = false
    query.value = ''
    selectedIndex.value = 0
}

const executeCommand = (command) => {
    if (!command) return

    if (command.expandsAll) {
        query.value = '/list '
        return
    }

    command.action()
    close()
}

const handleKeydown = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault()
        if (isOpen.value) {
            close()
        } else {
            open()
        }
    }
}

watch(query, () => {
    selectedIndex.value = 0
})

onMounted(() => document.addEventListener('keydown', handleKeydown))
onUnmounted(() => document.removeEventListener('keydown', handleKeydown))

defineExpose({ open, close })
</script>

<style scoped>
.overflow-y-auto {
    scroll-behavior: smooth;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.2);
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.4);
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.15);
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.3);
}
</style>
