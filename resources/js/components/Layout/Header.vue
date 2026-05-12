<template>
    <div
        class="fixed bg-white dark:bg-neutral-950 flex items-center w-full h-[70px] z-10 border-b border-gray-200 dark:border-neutral-800 loops-layout-header"
    >
        <div class="flex items-center justify-between w-full px-4 lg:px-6 mx-auto">
            <div class="flex items-center lg:w-[30%] w-auto">
                <button
                    @click="toggleMobileDrawer"
                    class="lg:hidden mr-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-800"
                >
                    <i class="bx bx-menu text-2xl dark:text-neutral-400"></i>
                </button>

                <router-link to="/" class="flex items-center gap-2">
                    <img
                        :src="appLogoUrl()"
                        alt="PornTk Logo"
                        class="rounded-full size-8 md:size-10"
                    />
                    <span
                        class="flex tracking-tight text-lg md:text-2xl font-bold text-black dark:text-white"
                    >
                        {{ appConfig.app.name }}
                    </span>
                </router-link>
            </div>

            <div
                class="flex items-center justify-end gap-2 lg:gap-3 lg:min-w-[275px] lg:max-w-[320px] w-auto"
            >
                <button
                    v-if="authStore.isAuthenticated"
                    @click="isLoggedIn"
                    class="hidden lg:flex items-center bg-primary border border-primary text-white rounded-lg px-5 py-2 hover:bg-primary/80 hover:border-primary/60 cursor-pointer"
                >
                    <i class="bx bx-upload text-white text-[22px]"></i>
                    <span class="px-2 font-medium tracking-tight text-[15px]">{{
                        t('nav.upload')
                    }}</span>
                </button>

                <div v-if="!authStore.isAuthenticated" class="hidden lg:flex items-center">
                    <ThemeToggleButton class="mr-1" />

                    <button
                        v-if="!authStore.isAuthenticated && appConfig.registration"
                        @click="authStore.openAuthModal('register')"
                        class="flex items-center border-primary text-primary border dark:border-red-400 rounded-md px-3 py-[6px] cursor-pointer mr-3"
                    >
                        <span class="mx-4 font-medium text-[15px]">{{ t('nav.join') }}</span>
                    </button>

                    <button
                        v-if="!authStore.isAuthenticated"
                        @click="authStore.openAuthModal('login')"
                        class="flex items-center bg-primary text-white border dark:border-red-400 rounded-md px-3 py-[6px] cursor-pointer"
                    >
                        <span class="mx-4 font-medium text-[15px]">{{ t('nav.logIn') }}</span>
                    </button>
                </div>

                <div v-if="authStore.isAuthenticated" class="hidden lg:flex items-center">
                    <div class="relative">
                        <button
                            class="mt-1 cursor-pointer hover:opacity-90"
                            @click="showMenu = !showMenu"
                        >
                            <img
                                class="rounded-full"
                                width="33"
                                :src="authStore.user.avatar"
                                alt="User avatar"
                                @error="$event.target.src = '/storage/avatars/default.jpg'"
                            />
                        </button>

                        <div
                            v-if="showMenu"
                            class="absolute bg-white dark:bg-neutral-900 rounded-lg w-[200px] shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700 ark:border-neutral-700 top-[43px] -right-2"
                        >
                            <router-link
                                :to="`/@${authStore.user.username}`"
                                @click="showMenu = false"
                                class="flex items-center justify-start py-3 px-2 hover:bg-gray-100 dark:hover:bg-neutral-800 cursor-pointer dark:text-neutral-200"
                            >
                                <i class="ph-user text-[20px]"></i>
                                <span class="pl-2 font-semibold text-sm">{{
                                    t('nav.profile')
                                }}</span>
                            </router-link>
                            <div
                                @click="logout"
                                class="flex items-center justify-start py-3 px-1.5 hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-800 cursor-pointer"
                            >
                                <i class="ic-outline-login text-[20px]"></i>
                                <span class="pl-2 font-semibold text-sm">{{
                                    t('nav.logOut')
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    v-if="authStore.isAuthenticated"
                    @click="toggleMobileDrawer"
                    class="lg:hidden"
                >
                    <img
                        class="rounded-full"
                        width="32"
                        :src="authStore.user.avatar"
                        @error="$event.target.src = '/storage/avatars/default.jpg'"
                        alt="User avatar"
                    />
                </button>

                <button
                    v-if="!authStore.isAuthenticated"
                    @click="authStore.openAuthModal('login')"
                    class="lg:hidden bg-primary text-white rounded-md px-3 py-2 text-sm font-medium"
                >
                    {{ t('nav.loginOrRegister') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, inject, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useSearchStore } from '@/stores/search'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '~/stores/notifications'
import { useUtils } from '@/composables/useUtils'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

const emit = defineEmits(['toggleMobileDrawer', 'openLogin'])

const { formatCount } = useUtils()
const router = useRouter()
const authStore = useAuthStore()
const searchStore = useSearchStore()
const notificationsStore = useNotificationStore()
const appConfig = inject('appConfig')

const showMenu = ref(false)
const searchQuery = ref('')
const showDropdown = ref(false)
const showMobileSearch = ref(false)
const mobileSearchInput = ref(null)

const appLogoUrl = () => {
    return appConfig.branding.logo || `/nav-logo.png`
}

const toggleMobileDrawer = () => {
    emit('toggleMobileDrawer')
}

const toggleMobileSearch = async () => {
    searchStore.clearSearchResults()
    showMobileSearch.value = !showMobileSearch.value
    if (showMobileSearch.value) {
        await nextTick()
        mobileSearchInput.value?.focus()
    }
}

const closeMobileSearch = () => {
    searchStore.clearSearchResults()
    showMobileSearch.value = false
    searchQuery.value = ''
    showDropdown.value = false
}

onMounted(() => {
    // searchStore.loadRecentSearches()

    document.addEventListener('mouseup', (e) => {
        const searchContainer = document.querySelector('.search-container')
        const popupMenu = document.getElementById('PopupMenu')

        if (searchContainer && !searchContainer.contains(e.target)) {
            showDropdown.value = false
        }

        if (popupMenu && !popupMenu.contains(e.target)) {
            showMenu.value = false
        }

        if (showMobileSearch.value && !e.target.closest('.absolute.top-full')) {
            closeMobileSearch()
        }
    })
})

const handleSearch = useDebounceFn(() => {
    searchStore.searchQuery(searchQuery.value)
}, 300)

const handleFocus = () => {
    showDropdown.value = true
}

const handleRecentSearchClick = (query) => {
    searchQuery.value = query
    searchStore.searchQuery(query)
    if (showMobileSearch.value) {
        closeMobileSearch()
    }
}

const handleResultClick = (result) => {
    console.log('result', result)
    searchQuery.value = result.title
    showDropdown.value = false
    router.push(`/@${result.username}`)
    if (showMobileSearch.value) {
        closeMobileSearch()
    }
}

const displayCount = (count) => {
    return count > 99 ? '99+' : count.toString()
}

const isLoggedIn = () => {
    if (authStore.isAuthenticated) {
        router.push('/studio/upload')
    } else {
        router.push('/login?redirect=/login')
    }
}

const logout = async () => {
    try {
        await authStore.logout()
        router.push('/')
        showMenu.value = false
    } catch (error) {
        console.log(error)
    }
}
</script>
