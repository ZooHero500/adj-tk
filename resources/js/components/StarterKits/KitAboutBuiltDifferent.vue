<template>
    <div v-if="loading" class="flex items-center justify-center h-30">
        <Spinner />
    </div>
    <section v-else class="py-32 max-w-6xl mx-auto px-6">
        <div class="text-center mb-20">
            <p
                class="text-xs font-semibold uppercase tracking-[0.2em] dark:text-white/35 text-black/40 mb-5"
            >
                Why Starter Kits
            </p>
            <h2
                class="font-display text-5xl md:text-[4rem] font-black tracking-tight leading-[0.92] mb-6"
            >
                Built different.
            </h2>
            <p
                class="dark:text-white/50 text-black/50 text-lg max-w-md lg:max-w-2xl mx-auto leading-relaxed"
            >
                Not just another follow suggestion. Community-powered,
                <span v-if="config.requires_admin_approval">admin-reviewed,</span> consent-driven,
                and fediverse compatible from day one.
            </p>
        </div>

        <div
            class="consent-card group relative p-8 md:p-10 rounded-2xl border dark:border-white/[0.07] border-black/[0.07] dark:bg-white/[0.03] bg-black/[0.03] mb-4 overflow-hidden cursor-default"
        >
            <div
                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                style="
                    background: linear-gradient(
                        130deg,
                        rgba(255, 99, 71, 0.06) 0%,
                        rgba(255, 20, 120, 0.04) 100%
                    );
                "
            ></div>

            <div class="relative flex flex-col md:flex-row md:items-start gap-8">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-5">
                        <div
                            class="w-11 h-11 rounded-xl dark:bg-white/10 bg-black/8 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 dark:text-white/70 text-black/60 shrink-0"
                        >
                            <CheckBadgeIcon class="h-6 w-6" />
                        </div>
                        <span
                            class="text-xs font-semibold uppercase tracking-[0.18em] gradient-text"
                            >Consent-driven by design</span
                        >
                    </div>
                    <h3
                        class="font-display text-2xl md:text-3xl font-black tracking-tight mb-4 leading-tight"
                    >
                        Accounts choose how they're included.
                    </h3>
                    <p
                        class="dark:text-white/50 text-black/50 leading-relaxed text-[0.95rem] max-w-lg"
                    >
                        Only accounts that support Starter Kits can appear in one. No one gets added
                        without a platform that honours their preferences — and every creator gets
                        full control over how and whether they participate.
                    </p>
                    <div class="flex flex-col lg:flex-row gap-2.5 mt-8">
                        <div
                            class="privacy-pill flex items-center gap-3 px-4 py-3 rounded-xl dark:bg-white/[0.04] bg-black/[0.04] border dark:border-white/[0.06] border-black/[0.06]"
                        >
                            <span
                                class="w-2 h-2 rounded-full bg-emerald-400/80 shrink-0 shadow-[0_0_6px_rgba(52,211,153,0.6)]"
                            ></span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold dark:text-white/80 text-black/70">
                                    Open
                                </p>
                                <p
                                    class="text-[0.7rem] dark:text-white/35 text-black/35 leading-snug"
                                >
                                    Auto-approve all requests
                                </p>
                            </div>
                        </div>
                        <div
                            class="privacy-pill flex items-center gap-3 px-4 py-3 rounded-xl dark:bg-white/[0.04] bg-black/[0.04] border dark:border-white/[0.06] border-black/[0.06]"
                        >
                            <span
                                class="w-2 h-2 rounded-full bg-rose-400/80 shrink-0 shadow-[0_0_6px_rgba(251,191,36,0.6)]"
                            ></span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold dark:text-white/80 text-black/70">
                                    Restricted
                                </p>
                                <p
                                    class="text-[0.7rem] dark:text-white/35 text-black/35 leading-snug"
                                >
                                    Accounts I Follow Only
                                </p>
                            </div>
                        </div>
                        <div
                            class="privacy-pill flex items-center gap-3 px-4 py-3 rounded-xl dark:bg-white/[0.04] bg-black/[0.04] border dark:border-white/[0.06] border-black/[0.06]"
                        >
                            <span
                                class="w-2 h-2 rounded-full bg-orange-400/80 shrink-0 shadow-[0_0_6px_rgba(251,191,36,0.6)]"
                            ></span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold dark:text-white/80 text-black/70">
                                    Limited
                                </p>
                                <p
                                    class="text-[0.7rem] dark:text-white/35 text-black/35 leading-snug"
                                >
                                    Local Accounts Only
                                </p>
                            </div>
                        </div>
                        <div
                            class="privacy-pill flex items-center gap-3 px-4 py-3 rounded-xl dark:bg-white/[0.04] bg-black/[0.04] border dark:border-white/[0.06] border-black/[0.06]"
                        >
                            <span
                                class="w-2 h-2 rounded-full bg-amber-400/80 shrink-0 shadow-[0_0_6px_rgba(251,191,36,0.6)]"
                            ></span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold dark:text-white/80 text-black/70">
                                    Review
                                </p>
                                <p
                                    class="text-[0.7rem] dark:text-white/35 text-black/35 leading-snug"
                                >
                                    Approve inclusions manually
                                </p>
                            </div>
                        </div>
                        <div
                            class="privacy-pill flex items-center gap-3 px-4 py-3 rounded-xl dark:bg-white/[0.04] bg-black/[0.04] border dark:border-white/[0.06] border-black/[0.06]"
                        >
                            <span
                                class="w-2 h-2 rounded-full bg-red-400/80 shrink-0 shadow-[0_0_6px_rgba(248,113,113,0.6)]"
                            ></span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold dark:text-white/80 text-black/70">
                                    Closed
                                </p>
                                <p
                                    class="text-[0.7rem] dark:text-white/35 text-black/35 leading-snug"
                                >
                                    Decline all requests
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div
                v-for="f in features"
                :key="f.title"
                :class="[
                    'feature-card group p-8 rounded-2xl border dark:border-white/[0.07] border-black/[0.07] dark:bg-white/[0.03] bg-black/[0.03] hover:dark:bg-white/[0.06] hover:bg-black/[0.05] transition-all duration-300 cursor-default',
                    f.wide ? 'md:col-span-2' : ''
                ]"
            >
                <div
                    class="w-11 h-11 rounded-xl dark:bg-white/10 bg-black/8 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 dark:text-white/70 text-black/60"
                >
                    <component :is="f.icon" class="h-6 w-6" />
                </div>
                <h3 class="font-display text-xl font-bold mb-3 tracking-tight">{{ f.title }}</h3>
                <p class="dark:text-white/50 text-black/50 leading-relaxed text-[0.95rem]">
                    {{ f.desc }}
                </p>
                <p
                    v-if="f.note"
                    class="mt-3 text-[0.8rem] dark:text-white/25 text-black/30 leading-relaxed italic"
                >
                    {{ f.note }}
                </p>
            </div>
        </div>
    </section>
</template>

<script setup>
import axios from '@/plugins/axios'
import { onMounted, ref, computed } from 'vue'
import Spinner from '../Spinner.vue'
import {
    CheckBadgeIcon,
    HashtagIcon,
    BoltIcon,
    UserGroupIcon,
    GlobeAltIcon,
    ShieldCheckIcon,
    FlagIcon
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const config = ref({})
const api = axios.getAxiosInstance()

const fetchConfig = async () => {
    try {
        const res = await api.get('/api/v1/starter-kits/config')
        config.value = res.data.data
    } catch (err) {
    } finally {
        loading.value = false
    }
}

const features = computed(() => {
    const {
        requires_admin_approval,
        auto_approve_enabled,
        auto_approve_follower_threshold,
        min_followers_required
    } = config.value

    const communityDesc = (() => {
        if (auto_approve_enabled && min_followers_required == 0) {
            return `Built and submitted by real people on PornTk or across the fediverse. You can create a new Starter Kit instantly — no waiting, no friction.`
        }
        if (
            auto_approve_enabled &&
            auto_approve_follower_threshold > 1 &&
            min_followers_required > 1
        ) {
            return `Built and submitted by real people on PornTk or across the fediverse. Creators with ${auto_approve_follower_threshold}+ followers get instant approval — everyone else goes through a quick review. High signal by design.`
        }
        if (auto_approve_enabled && auto_approve_follower_threshold > 1) {
            return `Built and submitted by real people on PornTk or across the fediverse. Creators with ${auto_approve_follower_threshold}+ followers are approved instantly — no waiting, no friction.`
        }
        if (min_followers_required > 1) {
            return `Every kit is built and submitted by established people on PornTk or across the fediverse — you need at least ${min_followers_required} followers to contribute. Reviewed before going live. High signal by design, not by accident.`
        }
        return 'Every kit is built and submitted by real people on PornTk or across the fediverse — then reviewed before going live. High signal by design, not by accident.'
    })()

    return [
        {
            title: 'Discover by hashtag',
            desc: "Search or tap any hashtag to surface kits built around it - whether it's #FilmPhotography, #IndieGames, or #QueerCreators.",
            icon: HashtagIcon
        },
        {
            title: 'One-tap follow all',
            desc: 'Follow every creator in a kit instantly. Go from a blank feed to a thriving one in seconds — no hours of endless searching required.',
            icon: BoltIcon
        },
        {
            title: 'Curated by the community',
            desc: communityDesc,
            icon: UserGroupIcon
        },
        {
            title: 'Fediverse focused',
            desc: "Starter Kits aren't limited to one server. Use them across the fediverse, where supported - because great creators and content knows no borders.",
            icon: GlobeAltIcon
        },
        {
            title: requires_admin_approval ? 'Admin reviewed' : 'Community moderated',
            desc: requires_admin_approval
                ? 'Before any kit goes public it passes a moderation queue. New kits and media updates both require approval — quality and trust are built in from day one, not patched in later.'
                : "No gatekeepers - anyone can create a kit that abides by the rules. If something doesn't belong, report it and a human will take a look.",
            icon: ShieldCheckIcon
        },
        {
            title: 'Easy reporting',
            desc: "Spotted a kit that doesn't belong? One tap to report it. Every report lands directly in the moderation queue so bad actors get dealt with.",
            icon: FlagIcon
        }
    ]
})

onMounted(() => fetchConfig())
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.gradient-text {
    background: linear-gradient(130deg, #ff6347 0%, #ff1478 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.consent-card {
    position: relative;
}

.consent-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 1rem;
    padding: 1px;
    background: linear-gradient(
        130deg,
        rgba(255, 99, 71, 0.25) 0%,
        rgba(255, 20, 120, 0.15) 50%,
        transparent 100%
    );
    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
    opacity: 0.6;
    transition: opacity 0.3s ease;
}

.consent-card:hover::before {
    opacity: 1;
}

.feature-card {
    position: relative;
}

.feature-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 1rem;
    opacity: 0;
    transition: opacity 0.3s ease;
    background: linear-gradient(130deg, rgba(255, 99, 71, 0.08) 0%, rgba(255, 20, 120, 0.06) 100%);
    pointer-events: none;
}

.feature-card:hover::after {
    opacity: 1;
}

.privacy-pill {
    transition:
        transform 0.2s ease,
        background 0.2s ease;
}

.privacy-pill:hover {
    transform: translateX(3px);
}
</style>
