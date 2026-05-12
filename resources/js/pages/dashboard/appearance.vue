<template>
    <SettingsLayout>
        <div class="p-6">
            <h1 class="text-2xl font-semibold tracking-tight mb-6 dark:text-gray-100">
                {{ $t('nav.appearance') }}
            </h1>
            <hr class="border-gray-300 dark:border-gray-700" />

            <section class="my-8">
                <h2 class="tracking-tight font-light mb-4 dark:text-gray-300">
                    {{ $t('common.theme') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button @click="isDark ? handleToggleDarkMode() : null" class="relative group">
                        <div
                            class="bg-white rounded-xl border-2 transition-all duration-200 p-4 text-left"
                            :class="
                                !isDark
                                    ? 'border-red-500 shadow-lg shadow-red-500/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                            "
                        >
                            <div
                                class="aspect-video bg-gray-50 rounded-lg border border-gray-200 overflow-hidden mb-3 relative"
                            >
                                <div class="absolute inset-0 flex">
                                    <div
                                        class="w-[25%] bg-white border-r border-gray-200 p-2 flex flex-col gap-2"
                                    >
                                        <div class="h-2 bg-gray-300 rounded w-3/4"></div>
                                        <div class="h-1.5 bg-gray-200 rounded w-2/3 mt-1"></div>
                                        <div class="h-1.5 bg-gray-200 rounded w-2/3"></div>
                                        <div class="h-1.5 bg-gray-200 rounded w-2/3"></div>
                                    </div>

                                    <div class="flex-1 bg-gray-100 relative">
                                        <div
                                            class="absolute inset-2 right-8 bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg"
                                        >
                                            <div
                                                class="absolute bottom-1.5 left-1.5 right-10 space-y-0.5"
                                            >
                                                <div class="h-1.5 bg-white/90 rounded w-1/3"></div>
                                                <div class="h-1 bg-white/80 rounded w-2/3"></div>
                                            </div>
                                        </div>

                                        <div class="absolute right-2 bottom-2 flex flex-col gap-2">
                                            <div
                                                class="w-4 h-4 rounded-full bg-white border border-gray-300"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-white border border-gray-300"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-white border border-gray-300"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-white border border-gray-300"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-white border border-gray-300"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-white border border-gray-300"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-0.5">
                                        {{ $t('common.light') }}
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $t('settings.classicBrightTheme') }}
                                    </p>
                                </div>
                                <div
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                    :class="
                                        !isDark ? 'border-red-500 bg-red-500' : 'border-gray-300'
                                    "
                                >
                                    <svg
                                        v-if="!isDark"
                                        class="w-3 h-3 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </button>

                    <button @click="!isDark ? handleToggleDarkMode() : null" class="relative group">
                        <div
                            class="bg-white dark:bg-neutral-950 rounded-xl border-2 transition-all duration-200 p-4 text-left"
                            :class="
                                isDark
                                    ? 'border-red-500 shadow-lg shadow-red-500/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                            "
                        >
                            <div
                                class="aspect-video bg-black rounded-lg border border-neutral-800 overflow-hidden mb-3 relative"
                            >
                                <div class="absolute inset-0 flex">
                                    <div
                                        class="w-[25%] bg-neutral-950 border-r border-neutral-800 p-2 flex flex-col gap-2"
                                    >
                                        <div class="h-2 bg-neutral-700 rounded w-3/4"></div>
                                        <div class="h-1.5 bg-neutral-800 rounded w-2/3 mt-1"></div>
                                        <div class="h-1.5 bg-neutral-800 rounded w-2/3"></div>
                                        <div class="h-1.5 bg-neutral-800 rounded w-2/3"></div>
                                    </div>

                                    <div class="flex-1 bg-neutral-900 relative">
                                        <div
                                            class="absolute inset-2 right-8 bg-gradient-to-br from-neutral-700 to-neutral-800 rounded-lg"
                                        >
                                            <div
                                                class="absolute bottom-1.5 left-1.5 right-10 space-y-0.5"
                                            >
                                                <div class="h-1.5 bg-white/90 rounded w-1/3"></div>
                                                <div class="h-1 bg-white/70 rounded w-2/3"></div>
                                            </div>
                                        </div>

                                        <div class="absolute flex flex-col gap-2 right-2 bottom-2">
                                            <div
                                                class="w-4 h-4 rounded-full bg-neutral-800 border border-neutral-600"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-neutral-800 border border-neutral-600"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-neutral-800 border border-neutral-600"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-neutral-800 border border-neutral-600"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-neutral-800 border border-neutral-600"
                                            ></div>
                                            <div
                                                class="w-4 h-4 rounded-full bg-neutral-800 border border-neutral-600"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-0.5">
                                        {{ $t('common.dark') }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $t('settings.darkAndEasyOnTheEyes') }}
                                    </p>
                                </div>
                                <div
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                    :class="
                                        isDark
                                            ? 'border-red-500 bg-red-500'
                                            : 'border-gray-300 dark:border-gray-600'
                                    "
                                >
                                    <svg
                                        v-if="isDark"
                                        class="w-3 h-3 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>
            </section>

            <section class="my-8">
                <h2 class="tracking-tight font-light mb-4 dark:text-gray-300">
                    {{ $t('language.language') }}
                </h2>
                <div class="flex flex-col gap-3 mb-6">
                    <div class="bg-white dark:bg-neutral-950 rounded-lg shadow-sm">
                        <div class="px-4 py-6">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                            >
                                <div class="flex flex-col max-w-[80%]">
                                    <h3 class="font-medium mb-2 dark:text-gray-300">
                                        {{ $t('language.displayLanguage') }}
                                    </h3>
                                    <p class="text-xs text-gray-500 font-light">
                                        {{ $t('language.choseYourPreferredLanguage') }}
                                    </p>
                                </div>
                                <div class="sm:min-w-[200px]">
                                    <select
                                        v-model="selectedLocale"
                                        @change="changeLanguage"
                                        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:border-gray-600 dark:bg-neutral-900 dark:text-white dark:focus:border-red-400 dark:focus:ring-red-400 transition-colors"
                                    >
                                        <option
                                            v-for="language in availableLanguages"
                                            :key="language.code"
                                            :value="language.code"
                                        >
                                            {{ language.nativeName }} ({{ language.name }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div
                                v-if="currentLanguage"
                                class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800"
                            >
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">{{
                                        $t('language.picker.current')
                                    }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ currentLanguage.nativeName }}
                                    </span>
                                    <span class="text-gray-400 dark:text-gray-500">
                                        ({{ currentLanguage.name }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="my-8">
                <h2 class="tracking-tight font-light mb-4 dark:text-gray-300">
                    {{ $t('settings.feeds') }}
                </h2>
                <div class="flex flex-col gap-3 mb-6">
                    <div class="bg-white dark:bg-neutral-950 rounded-lg shadow-sm">
                        <div class="px-4 py-6 flex items-center justify-between">
                            <div class="flex flex-col max-w-[80%]">
                                <h3 class="font-medium mb-2 dark:text-gray-300">
                                    {{ $t('settings.keepCommentDrawerOpen') }}
                                </h3>
                                <p class="text-xs text-gray-500 font-light">
                                    {{ $t('settings.keepCommentDrawerOpenHelpText') }}
                                </p>
                            </div>
                            <ToggleSwitch v-model="keepCommentDrawerOpen" />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </SettingsLayout>
</template>

<script setup>
import { onMounted, ref, nextTick, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import SettingsLayout from '~/layouts/SettingsLayout.vue'
import ToggleSwitch from '@/components/Form/ToggleSwitch.vue'
import axios from '~/plugins/axios'
import { useCommentStore } from '@/stores/comments'
const commentStore = useCommentStore()
const { locale, availableLocales, t } = useI18n()
const axiosInstance = axios.getAxiosInstance()
const keepCommentDrawerOpen = ref(commentStore.shouldKeepCommentsOpen)
const isDark = ref(document.documentElement.classList.contains('dark'))
const selectedLocale = ref(locale.value)

const languageInfo = {
    af: {
        name: 'Afrikaans',
        nativeName: 'Afrikaans'
    },
    ar: {
        name: 'Arabic',
        nativeName: 'العربية'
    },
    ca: {
        name: 'Catalan',
        nativeName: 'Català'
    },
    cs: {
        name: 'Czech',
        nativeName: 'Čeština'
    },
    da: {
        name: 'Danish',
        nativeName: 'Dansk'
    },
    de: {
        name: 'German',
        nativeName: 'Deutsch'
    },
    el: {
        name: 'Greek',
        nativeName: 'Ελληνικά'
    },
    en: {
        name: 'English',
        nativeName: 'English'
    },
    es: {
        name: 'Spanish',
        nativeName: 'Español'
    },
    fi: {
        name: 'Finnish',
        nativeName: 'Suomi'
    },
    fr: {
        name: 'French',
        nativeName: 'Français'
    },
    he: {
        name: 'Hebrew',
        nativeName: 'עברית'
    },
    hu: {
        name: 'Hungarian',
        nativeName: 'Magyar'
    },
    it: {
        name: 'Italian',
        nativeName: 'Italiano'
    },
    ja: {
        name: 'Japanese',
        nativeName: '日本語'
    },
    ko: {
        name: 'Korean',
        nativeName: '한국어'
    },
    nl: {
        name: 'Dutch',
        nativeName: 'Nederlands'
    },
    no: {
        name: 'Norwegian',
        nativeName: 'Norsk'
    },
    pl: {
        name: 'Polish',
        nativeName: 'Polski'
    },
    pt: {
        name: 'Portuguese',
        nativeName: 'Português'
    },
    ro: {
        name: 'Romanian',
        nativeName: 'Română'
    },
    ru: {
        name: 'Russian',
        nativeName: 'Русский'
    },
    sr: {
        name: 'Serbian',
        nativeName: 'Српски'
    },
    sv: {
        name: 'Swedish',
        nativeName: 'Svenska'
    },
    tr: {
        name: 'Turkish',
        nativeName: 'Türkçe'
    },
    uk: {
        name: 'Ukrainian',
        nativeName: 'Українська'
    },
    vi: {
        name: 'Vietnamese',
        nativeName: 'Tiếng Việt'
    },
    zh: {
        name: 'Chinese',
        nativeName: '中文'
    }
}

const availableLanguages = computed(() => {
    return availableLocales
        .filter((loc) => languageInfo[loc])
        .map((loc) => ({
            code: loc,
            ...languageInfo[loc]
        }))
})

const currentLanguage = computed(() => {
    return (
        languageInfo[locale.value] || {
            name: locale.value,
            nativeName: locale.value
        }
    )
})

const handleToggleDarkMode = () => {
    isDark.value = !isDark.value
    if (isDark.value) {
        document.documentElement.classList.add('dark')
        localStorage.setItem('theme', 'dark')
    } else {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('theme', 'light')
    }
}

const changeLanguage = () => {
    if (selectedLocale.value !== locale.value) {
        locale.value = selectedLocale.value

        localStorage.setItem('preferred-language', selectedLocale.value)

        document.documentElement.lang = selectedLocale.value

        window.dispatchEvent(
            new CustomEvent('language-changed', {
                detail: { locale: selectedLocale.value }
            })
        )

        setTimeout(() => {
            window.location.reload()
        }, 500)
    }
}

const toggleKeepCommentsOpen = async () => {
    commentStore.setKeepCommentsOpen(keepCommentDrawerOpen.value)
}

watch(keepCommentDrawerOpen, (newValue) => {
    commentStore.setKeepCommentsOpen(newValue)
})

onMounted(() => {
    const savedLanguage = localStorage.getItem('preferred-language')
    if (savedLanguage && availableLocales.includes(savedLanguage)) {
        locale.value = savedLanguage
        selectedLocale.value = savedLanguage
        document.documentElement.lang = savedLanguage
    }
})
</script>
