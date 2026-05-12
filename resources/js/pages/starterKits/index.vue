<template>
    <MainLayout>
        <div
            v-if="loading"
            class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body lg:-mt-5 lg:-mr-2.5 w-full h-full flex flex-col justify-center items-center"
        >
            <Spinner />
        </div>

        <KitFeatureDisabled v-else-if="isDisabled && !loading" />

        <KitGuestLanding v-else-if="!authStore.isAuthenticated" :stats="guestStats" />

        <div
            v-else
            class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] font-body lg:-mt-5 lg:-mr-2.5"
        >
            <div class="max-w-7xl mx-auto px-6 md:px-12 pt-14 pb-12">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2.5 mb-5">
                            <span class="block w-8 h-px bg-primary"></span>
                            <span
                                class="text-xs font-semibold tracking-[0.15em] uppercase text-primary"
                                >{{ t('common.starterKits') }}</span
                            >
                        </div>
                        <h1
                            class="font-display text-5xl md:text-6xl font-bold text-gray-950 dark:text-white leading-[1.05] tracking-tight mb-4"
                        >
                            {{ t('common.findYourPeople') }},<br />
                            <span class="text-primary">{{ t('common.instantly') }}.</span>
                        </h1>
                        <p
                            class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed max-w-lg"
                        >
                            {{ t('common.curatedCollectionsOfCreatorsWorthFollowingWithOneTap') }}
                        </p>
                    </div>

                    <KitHomeDropdown />
                </div>

                <div
                    class="flex items-center gap-6 mt-10 pt-8 border-t border-gray-200 dark:border-gray-800"
                >
                    <div class="flex items-baseline gap-1.5">
                        <span
                            class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                            >{{ formatCount(stats.total_kits) }}</span
                        >
                        <span class="text-sm text-gray-400">{{ t('common.activeKits') }}</span>
                    </div>
                    <div class="w-px h-5 bg-gray-200 dark:bg-gray-800"></div>
                    <div class="flex items-baseline gap-1.5">
                        <span
                            class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                            >{{ formatCount(stats.total_uses) }}</span
                        >
                        <span class="text-sm text-gray-400">{{ t('common.timesUsed') }}</span>
                    </div>
                    <div class="w-px h-5 bg-gray-200 dark:bg-gray-800"></div>
                    <div class="flex items-baseline gap-1.5">
                        <span
                            class="text-2xl font-bold font-display text-gray-900 dark:text-white"
                            >{{ formatCount(stats.total_accounts) }}</span
                        >
                        <span class="text-sm text-gray-400">{{ t('common.accountsCurated') }}</span>
                    </div>
                </div>
            </div>

            <div v-if="myKits && myKits.length" class="mb-14">
                <div class="max-w-7xl mx-auto px-6 md:px-12 mb-5 flex items-center justify-between">
                    <h2
                        class="font-display text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2"
                    >
                        <span class="w-2 h-2 rounded-full bg-primary inline-block"></span>
                        {{ t('common.myKits') }}
                    </h2>
                    <router-link
                        to="/starter-kits/my-kits"
                        class="text-sm font-semibold text-primary hover:underline flex items-center gap-1"
                    >
                        {{ t('common.viewAll') }}
                        <ChevronRightIcon class="w-4 h-4" />
                    </router-link>
                </div>
                <div
                    class="pl-6 md:pl-12 flex gap-4 overflow-x-auto pb-2 scrollbar-hide snap-x snap-mandatory"
                >
                    <div v-for="kit in myKits" :key="kit.id" class="flex-none w-72 snap-start">
                        <KitCarouselCard :kit="kit" :isMyKit="true" />
                    </div>
                    <div class="flex-none w-6"></div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2 space-y-14">
                        <section>
                            <div class="flex items-center justify-between mb-5">
                                <h2
                                    class="font-display text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2"
                                >
                                    <span
                                        class="w-2 h-2 rounded-full bg-amber-400 inline-block"
                                    ></span>
                                    {{ t('profile.tabFilterOptions.Popular') }}
                                </h2>
                                <router-link
                                    to="/starter-kits/browse"
                                    class="text-sm font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 flex items-center gap-1 transition-colors"
                                >
                                    {{ t('common.seeAll') }}
                                    <ChevronRightIcon class="w-4 h-4" />
                                </router-link>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <KitCompactCard
                                    v-for="kit in popularKits.slice(0, 4)"
                                    :key="kit.id"
                                    :kit="kit"
                                />
                            </div>
                        </section>

                        <section>
                            <div class="flex items-center justify-between mb-5">
                                <h2
                                    class="font-display text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2"
                                >
                                    <span
                                        class="w-2 h-2 rounded-full bg-sky-400 inline-block"
                                    ></span>
                                    {{ t('profile.tabFilterOptions.Latest') }}
                                </h2>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="kit in latestKits"
                                    :key="kit.id"
                                    class="group bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 hover:border-gray-200 dark:hover:border-gray-700 hover:shadow-lg hover:shadow-black/5 dark:hover:shadow-black/30 transition-all cursor-pointer"
                                >
                                    <div class="flex items-start gap-5">
                                        <div class="flex-shrink-0 flex items-center">
                                            <div class="relative w-[58px] h-[42px]">
                                                <img
                                                    v-for="(account, idx) in kit.accounts.slice(
                                                        0,
                                                        3
                                                    )"
                                                    :key="idx"
                                                    :src="account.avatar"
                                                    class="absolute top-0 w-9 h-9 rounded-full border-2 border-white dark:border-gray-900 object-cover shadow-sm"
                                                    :style="{
                                                        left: idx * 16 + 'px',
                                                        zIndex: 3 - idx
                                                    }"
                                                />
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="min-w-0">
                                                    <h3
                                                        class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors truncate"
                                                    >
                                                        {{ kit.title }}
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1"
                                                    >
                                                        {{ kit.description }}
                                                    </p>
                                                </div>
                                                <router-link
                                                    :to="kit.path"
                                                    class="flex-shrink-0 px-4 py-1.5 text-xs font-bold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 group-hover:bg-primary group-hover:border-primary group-hover:text-white transition-all"
                                                >
                                                    {{ t('common.useKit') }}
                                                </router-link>
                                            </div>

                                            <div class="flex items-center gap-4 mt-3">
                                                <div class="flex items-center gap-1.5">
                                                    <img
                                                        :src="kit.creator.avatar"
                                                        class="w-4 h-4 rounded-full"
                                                    />
                                                    <span class="text-xs text-gray-400"
                                                        >@{{ kit.creator.username }}</span
                                                    >
                                                </div>
                                                <div class="flex gap-1.5">
                                                    <span
                                                        v-for="tag in kit.hashtags.slice(0, 2)"
                                                        :key="tag"
                                                        class="text-[10px] font-medium px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-full"
                                                        >#{{ tag }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="ml-auto flex items-center gap-3 text-xs text-gray-400"
                                                >
                                                    <span class="flex items-center gap-1">
                                                        <UsersIcon class="w-3.5 h-3.5" />{{
                                                            formatCount(kit.approved_accounts)
                                                        }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <ArrowUpTrayIcon class="w-3.5 h-3.5" />{{
                                                            formatCount(kit.uses)
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="space-y-6">
                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                        >
                            <h3
                                class="font-display font-bold text-gray-900 dark:text-white mb-4 text-sm uppercase tracking-wider"
                            >
                                {{ t('common.topCreatorsThisWeek') }}
                            </h3>
                            <div class="space-y-3">
                                <div
                                    v-for="(creator, idx) in topCreators"
                                    :key="creator.id"
                                    class="flex items-center gap-3 group cursor-pointer"
                                >
                                    <span
                                        class="text-xs font-bold w-5 text-gray-300 dark:text-gray-600 text-center"
                                        >{{ idx + 1 }}</span
                                    >
                                    <img
                                        :src="creator.avatar"
                                        class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-primary transition-colors"
                                        >
                                            {{ creator.username }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ formatCount(creator.total_kits) }} kits
                                        </div>
                                    </div>
                                    <router-link
                                        :to="`/@${creator.username}`"
                                        class="text-xs font-semibold text-primary opacity-0 group-hover:opacity-100 transition-opacity"
                                        >{{ t('studio.view') }}</router-link
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-2xl p-5"
                        >
                            <h3
                                class="font-display font-bold text-gray-900 dark:text-white mb-4 text-sm uppercase tracking-wider"
                            >
                                {{ t('common.popularTags') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <router-link
                                    v-for="tag in popularTags"
                                    :key="tag.name"
                                    :to="`/starter-kits/hashtag/${tag.name}`"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-primary/10 hover:text-primary dark:hover:text-[#FF4571] border border-transparent hover:border-primary/20 transition-all"
                                    :style="{ fontSize: tag.size }"
                                >
                                    #{{ tag.name }}
                                </router-link>
                            </div>
                        </div>

                        <div class="rounded-2xl p-6 bg-gray-950 dark:bg-primary text-white">
                            <div
                                class="text-xs font-semibold tracking-wider uppercase opacity-50 mb-3"
                            >
                                {{ t('common.getStarted') }}
                            </div>
                            <h4 class="font-display text-lg font-bold leading-tight mb-2">
                                {{ t('common.shareYourFavouriteCreators') }}
                            </h4>
                            <p class="text-sm opacity-60 mb-5 leading-relaxed">
                                {{ t('common.buildAKitAndHelpNewUsersFindTheirCommunityOnLoops') }}.
                            </p>
                            <AnimatedButton
                                class="w-full"
                                @click="router.push('/starter-kits/create')"
                            >
                                {{ t('common.createAStarterKit') }}
                            </AnimatedButton>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-16"></div>
        </div>
    </MainLayout>
</template>

<script setup>
import KitCarouselCard from '@/components/StarterKits/KitCarouselCard.vue'
import MainLayout from '@/layouts/MainLayout.vue'
import {
    ArrowUpTrayIcon,
    ChevronRightIcon,
    GlobeAltIcon,
    PlusIcon,
    UsersIcon
} from '@heroicons/vue/24/outline'
import { ref, onMounted, inject } from 'vue'
import axios from '~/plugins/axios'
import { useI18n } from 'vue-i18n'
import { useUtils } from '@/composables/useUtils'
import { useRouter } from 'vue-router'
import AnimatedButton from '@/components/AnimatedButton.vue'
import KitFeatureDisabled from '@/components/StarterKits/KitFeatureDisabled.vue'

const { formatCount } = useUtils()
const api = axios.getAxiosInstance()

const { t } = useI18n()
const authStore = inject('authStore')
const appConfig = inject('appConfig')
const router = useRouter()
const myKits = ref()
const stats = ref()
const topCreators = ref([])
const popularKits = ref([])
const latestKits = ref([])
const loading = ref(true)
const popularTags = ref([])
const isDisabled = ref(false)

const guestStats = ref([
    { value: '0', label: t('common.curatedAccounts') },
    { value: '0', label: t('common.curatedKits') },
    { value: 'Up to 25', label: t('common.accountsPerKit') },
    { value: '1 tap', label: t('common.toFollowAll') }
    // { value: '∞', label: 'Fediverse reach' },
])

const fetchStats = async () => {
    if (appConfig.starterKits === false) {
        isDisabled.value = true
        loading.value = false
        return
    }

    try {
        const statsRes = await api.get(`/api/v1/starter-kits/stats`)
        stats.value = statsRes.data.data.stats

        const totalAccounts = statsRes.data.data.stats.total_accounts
        const totalKits = statsRes.data.data.stats.total_kits

        guestStats.value = [
            { value: totalAccounts.toString(), label: t('common.curatedAccounts') },
            { value: totalKits.toString(), label: t('common.curatedKits') },
            ...guestStats.value.slice(2)
        ]
    } catch (e) {
        if (e?.response.status === 404) {
            isDisabled.value = true
            loading.value = false
        }
    } finally {
        if (authStore.isAuthenticated) {
            fetchData()
        } else {
            loading.value = false
        }
    }
}

const fetchData = async () => {
    try {
        const [popularRes, latestRes, topCreatorsRes, topHashtagsRes] = await Promise.all([
            api.get('/api/v1/starter-kits/popular'),
            api.get('/api/v1/starter-kits/latest'),
            api.get(`/api/v1/starter-kits/top-creators`),
            api.get('/api/v1/starter-kits/hashtag/popular')
        ])
        popularKits.value = popularRes.data.data
        latestKits.value = latestRes.data.data
        topCreators.value = topCreatorsRes.data.data.top_creators
        popularTags.value = topHashtagsRes.data.data
    } catch (e) {
        if (e?.response.status === 404) {
            isDisabled.value = true
        }
    } finally {
        fetchMyKits()
    }
}

const fetchMyKits = async () => {
    try {
        const res = await api.get('/api/v1/starter-kits/my-kits', { params: { limit: 6 } })
        myKits.value = res.data.data
    } catch (e) {
    } finally {
        loading.value = false
    }
}

onMounted(fetchStats)
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
