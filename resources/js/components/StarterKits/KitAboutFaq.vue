<template>
    <section
        id="faq"
        class="py-32 dark:bg-white/[0.02] bg-black/[0.025] dark:border-y dark:border-white/[0.05] border-y border-black/[0.05]"
    >
        <div class="max-w-2xl mx-auto px-6">
            <div class="text-center mb-20">
                <p
                    class="text-xs font-semibold uppercase tracking-[0.2em] dark:text-white/35 text-black/40 mb-5"
                >
                    FAQ
                </p>
                <h2
                    class="font-display text-5xl md:text-[4rem] font-black tracking-tight leading-[0.92]"
                >
                    Questions?
                </h2>
            </div>

            <div>
                <div
                    v-for="(faq, i) in faqs"
                    :key="i"
                    class="dark:border-b dark:border-white/[0.07] border-b border-black/[0.07] first:dark:border-t first:border-t first:dark:border-white/[0.07] first:border-black/[0.07]"
                >
                    <button
                        @click="toggleFaq(i)"
                        class="w-full text-left py-6 flex items-start justify-between gap-6 group cursor-pointer"
                        :aria-expanded="openIndex === i"
                    >
                        <span
                            class="font-display text-[1.05rem] font-semibold leading-snug group-hover:gradient-text-toggle transition-all duration-200"
                        >
                            {{ faq.q }}
                        </span>
                        <ChevronUpIcon
                            v-if="openIndex === i"
                            class="w-5 h-5 shrink-0 mt-0.5 dark:text-white/35 text-black/35"
                        />
                        <ChevronDownIcon
                            v-else
                            class="w-5 h-5 shrink-0 mt-0.5 dark:text-white/35 text-black/35"
                        />
                    </button>

                    <div class="faq-body" :class="{ 'is-open': openIndex === i }">
                        <div class="faq-inner">
                            <p
                                class="pb-7 dark:text-white/55 text-black/55 leading-relaxed text-[0.95rem]"
                                v-html="faq.a"
                            ></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const openIndex = ref(null)

function toggleFaq(i) {
    openIndex.value = openIndex.value === i ? null : i
}

const faqs = [
    {
        q: 'What is a Starter Kit?',
        a: 'A Starter Kit is a curated collection of PornTk accounts organized around a theme, niche, or community. When you follow a kit, you instantly follow every account inside it — giving you a rich, focused feed right away. Think of it like a playlist, but for creators.'
    },
    {
        q: 'Who can create a Starter Kit?',
        a: 'Any PornTk user can submit a Starter Kit for review. Our admin team reviews each submission before it goes live to ensure it meets community quality standards. This keeps the directory high-signal and spam-free.'
    },
    {
        q: 'How do I add accounts to my kit?',
        a: 'When creating a Starter Kit, you add accounts by their handle. You can add up to 25 accounts and associate relevant hashtags to help people discover your Starter Kit through search.'
    },
    // {
    //     q: 'How do I add accounts to my kit?',
    //     a: 'When creating a Starter Kit, you add accounts by their handle — including accounts from other fediverse instances. You can add up to 25 accounts and associate relevant hashtags to help people discover your kit through search.'
    // },
    {
        q: 'How do I remove myself from a kit?',
        a: 'You can remove yourself from any Starter Kit on the <a href="/starter-kits/joined-kits" class="text-red-500 font-bold underline">Joined Kits</a> page.'
    },
    {
        q: 'What happens when a kit is updated after I follow it?',
        a: "Following a Starter Kit subscribes you to its approved accounts at that moment. If new accounts are added later, you'll get a notification and can choose to follow the additions."
    },
    {
        q: 'Can logged-out users browse Starter Kits?',
        a: 'Yes! Anyone can browse and preview Starter Kits without a PornTk account. Tapping Follow All will prompt them to sign in or join — making Starter Kits a great onboarding path for new users discovering the platform for the first time.'
    },
    {
        q: 'Can kits include accounts from other fediverse servers?',
        a: 'Not yet. We are working closely with the Mastodon project to ensure compatibility across our platforms, and expect this to be ready later this year.'
    },
    // {
    //     q: 'Can kits include accounts from other fediverse servers?',
    //     a: 'Absolutely. PornTk is built on ActivityPub, making it fully interoperable with the fediverse. Any compatible federated account can be featured in a kit, regardless of what server they call home, as long as they approve your request to be included in the kit.'
    // },
    {
        q: 'How do I get my kit featured in the directory?',
        a: 'Approved Starter Kits are eligible for featuring. A complete description, a strong header image, relevant hashtags, and a well-rounded account selection all help your kit stand out.'
    },
    {
        q: "Can I edit my kit after it's been approved?",
        a: 'Yes, with some caveats. Minor edits (adding or removing a few accounts, updating title, description and hashtags) do not require an admin review. Updating the header or icon images do require admin review before changes go live.'
    }
]
</script>

<style scoped>
.font-display {
    font-family: 'Syne', sans-serif;
}

.font-body {
    font-family: 'DM Sans', sans-serif;
}

.faq-body {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.faq-body.is-open {
    grid-template-rows: 1fr;
}

.faq-inner {
    overflow: hidden;
}
</style>
