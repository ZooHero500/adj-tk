<template>
    <SettingsLayout>
        <div class="p-6">
            <h1 class="text-2xl font-semibold tracking-tight mb-6 dark:text-gray-100">
                {{ $t('settings.safety') }}
            </h1>
            <hr class="border-gray-300 dark:border-gray-700" />

            <section class="my-8">
                <h2 class="tracking-tight font-light mb-4 dark:text-gray-300">
                    {{ $t('settings.blockingAndRestrictions') }}
                </h2>
                <div class="flex flex-col gap-3">
                    <router-link
                        to="/dashboard/safety/blocked-accounts"
                        class="bg-white dark:bg-neutral-950 rounded-lg shadow-sm"
                    >
                        <div class="flex justify-between items-center p-4">
                            <div class="flex items-center gap-5">
                                <h3 class="font-medium mb-0 dark:text-gray-300">
                                    {{ $t('settings.blockedAccounts') }}
                                </h3>
                                <p class="text-xs text-gray-500 -mb-1 font-light">
                                    {{ $t('settings.manageUsersYouHaveBlocked') }}
                                </p>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-2 text-gray-700 text-sm"
                                    >{{ blockedAccountsCount }} {{ $t('profile.blocked') }}</span
                                >
                                <i class="bx bx-chevron-right text-[20px] text-gray-400"></i>
                            </div>
                        </div>
                    </router-link>
                </div>
            </section>
        </div>
    </SettingsLayout>
</template>

<script setup>
import { onMounted, ref, nextTick } from 'vue'
import SettingsLayout from '~/layouts/SettingsLayout.vue'
import ToggleSwitch from '@/components/Form/ToggleSwitch.vue'
import axios from '~/plugins/axios'
const axiosInstance = axios.getAxiosInstance()
const hideSensitiveContent = ref(true)
const filterExplicitLanguage = ref(false)
const blockedAccountsCount = ref(3)

const fetchTotalBlocked = async () => {
    try {
        await axiosInstance.get('/api/v1/account/settings/total-blocked-accounts').then((res) => {
            blockedAccountsCount.value = res.data.data.count
        })
    } catch {}
}

onMounted(async () => {
    await fetchTotalBlocked()
    await nextTick()
})
</script>
