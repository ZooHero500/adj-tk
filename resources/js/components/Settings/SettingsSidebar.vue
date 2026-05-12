<template>
    <aside class="w-64 bg-white dark:bg-neutral-950">
        <nav class="flex flex-col h-full px-3">
            <div class="p-4">
                <router-link
                    class="flex items-center text-gray-500 hover:text-gray-400 cursor-pointer"
                    to="/"
                >
                    <ArrowLeftIcon class="w-4 h-4 mr-2" /> {{ t('settings.back') }}
                </router-link>
            </div>

            <div class="flex-1">
                <router-link
                    v-for="item in menuItems"
                    :key="item.name"
                    :to="item.path"
                    class="flex items-center p-4 py-2 font-light rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900"
                    :class="[
                        isRouteActive(item.path)
                            ? 'text-red-500 font-medium'
                            : 'text-gray-700 dark:text-gray-400'
                    ]"
                >
                    <component :is="item.icon" class="w-4 h-4 mr-3 text-gray-400" />
                    <span>{{ item.name }}</span>
                </router-link>
            </div>
        </nav>
    </aside>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import {
    ArrowLeftIcon,
    EyeSlashIcon,
    UserIcon,
    MoonIcon,
    LockClosedIcon,
    ShareIcon,
    CheckBadgeIcon,
    VideoCameraIcon
} from '@heroicons/vue/24/outline'

const { t } = useI18n()
const route = useRoute()

const menuItems = ref([
    {
        name: t('settings.manageAccount'),
        icon: UserIcon,
        path: '/dashboard/account'
    },
    {
        name: t('nav.appearance'),
        icon: MoonIcon,
        path: '/dashboard/appearance'
    },
    {
        name: 'Content',
        icon: VideoCameraIcon,
        path: '/dashboard/content'
    },
    {
        name: t('settings.privacy'),
        icon: EyeSlashIcon,
        path: '/dashboard/privacy'
    },
    {
        name: t('settings.safety'),
        icon: CheckBadgeIcon,
        path: '/dashboard/safety'
    },
    {
        name: t('settings.security'),
        icon: LockClosedIcon,
        path: '/dashboard/account/security'
    },
    {
        name: 'Sharing',
        icon: ShareIcon,
        path: '/dashboard/sharing'
    }
])

const isRouteActive = (navPath) => {
    const currentPath = route.path

    if (currentPath === navPath) {
        return true
    }

    if (currentPath.startsWith(navPath + '/')) {
        return true
    }

    return false
}
</script>
