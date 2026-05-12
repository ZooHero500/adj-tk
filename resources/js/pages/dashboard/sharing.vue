<template>
    <SettingsLayout>
        <div class="p-6">
            <h1 class="text-2xl font-semibold tracking-tight mb-6 dark:text-gray-100">
                {{ $t('settings.sharing') }}
            </h1>
            <hr class="border-gray-300 dark:border-gray-700" />

            <template v-if="loaded">
                <section class="my-8">
                    <h2 class="tracking-tight font-light mb-4 dark:text-gray-300">
                        {{ $t('settings.feeds') }}
                    </h2>
                    <div class="flex flex-col gap-3 mb-6">
                        <div class="bg-white dark:bg-neutral-950 rounded-lg shadow-sm">
                            <div class="px-4 py-6 flex items-center justify-between">
                                <div class="flex flex-col max-w-[80%]">
                                    <h3 class="font-medium mb-2 dark:text-gray-300">
                                        {{ $t('settings.enableAtomFeed') }}
                                    </h3>
                                    <p v-if="atomEnabled" class="text-xs text-gray-500 font-light">
                                        {{ $t('settings.enableAtomFeedHelpText') }}
                                    </p>
                                    <p v-else class="text-xs text-gray-500 font-light">
                                        This feature has been disabled by administrators.
                                    </p>
                                </div>
                                <ToggleSwitch :disabled="!atomEnabled" v-model="hasAtom" />
                            </div>

                            <div v-if="atomEnabled && hasAtom && atomUrl" class="px-4 pb-5">
                                <label
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 block"
                                >
                                    {{ $t('settings.yourFeedUrl') }}
                                </label>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="text"
                                        :value="atomUrl"
                                        disabled
                                        class="flex-1 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 select-all truncate"
                                    />
                                    <button
                                        @click="copyUrl"
                                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-2 text-sm font-medium transition-colors"
                                        :class="
                                            copied
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                                : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                                        "
                                    >
                                        <CheckIcon v-if="copied" class="w-4 h-4" />
                                        <ClipboardDocumentIcon v-else class="w-4 h-4" />
                                        {{
                                            copied
                                                ? $t('post.copiedExclamation')
                                                : $t('common.copy')
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>

            <template v-else>
                <section class="my-8">
                    <Spinner />
                </section>
            </template>
        </div>
    </SettingsLayout>
</template>

<script setup>
import { onMounted, ref, nextTick, watch, inject, computed } from 'vue'
import { ClipboardDocumentIcon, CheckIcon } from '@heroicons/vue/24/outline'
import SettingsLayout from '~/layouts/SettingsLayout.vue'
import ToggleSwitch from '@/components/Form/ToggleSwitch.vue'
import axios from '~/plugins/axios'

const appConfig = inject('appConfig')
const axiosInstance = axios.getAxiosInstance()
const hasAtom = ref(false)
const atomUrl = ref(null)
const loaded = ref(false)
const copied = ref(false)

const atomEnabled = computed(() => appConfig.atomFeeds === true)

const fetchSettings = async () => {
    if (!atomEnabled.value) {
        return
    }

    try {
        const res = await axiosInstance.get('/api/v1/account/sharing/settings')
        const data = res.data.data
        hasAtom.value = data.has_atom
        if (data.has_atom && data.atom_url) {
            atomUrl.value = data.atom_url
        }
    } catch {}
}

const copyUrl = async () => {
    try {
        await navigator.clipboard.writeText(atomUrl.value)
        copied.value = true
        setTimeout(() => (copied.value = false), 2000)
    } catch {}
}

watch(hasAtom, async (val) => {
    if (!loaded.value) return
    try {
        const res = await axiosInstance.post('/api/v1/account/sharing/settings', {
            has_atom: val
        })
        const data = res.data.data
        atomUrl.value = val && data.atom_url ? data.atom_url : null
    } catch {}
})

onMounted(async () => {
    await fetchSettings()
    await nextTick()
    loaded.value = true
})
</script>
