<template>
    <header
        v-if="isMobileView && showTabsAndSearch"
        class="fixed top-0 left-0 right-0 z-50 safe-area-top"
    >
        <div class="flex items-center justify-between h-14 px-4">
            <router-link to="/" class="flex items-center">
                <img src="/img/logo-light.svg" alt="PornTk" class="h-8 w-8 rounded-lg" />
            </router-link>
            <div class="relative" ref="dropdownRef">
                <button
                    @click="toggleDropdown"
                    class="flex items-center gap-1 px-4 py-1.5 rounded-full bg-black/40 backdrop-blur-sm text-white text-sm font-semibold transition-colors active:bg-black/60"
                >
                    {{ activeLabel }}
                    <i
                        class="bx bx-chevron-down text-lg transition-transform"
                        :class="{ 'rotate-180': isDropdownOpen }"
                    ></i>
                </button>

                <Transition name="dropdown">
                    <div
                        v-if="isDropdownOpen"
                        class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-44 bg-white dark:bg-neutral-800 rounded-xl shadow-xl overflow-hidden border border-gray-100 dark:border-neutral-700"
                    >
                        <button
                            v-for="tab in dropdownTabs"
                            :key="tab.key"
                            @click="selectTab(tab)"
                            class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium transition-colors"
                            :class="
                                activeTab === tab.key
                                    ? 'text-gray-900 dark:text-white bg-gray-50 dark:bg-neutral-700'
                                    : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-700'
                            "
                        >
                            <i :class="tab.icon" class="text-lg"></i>
                            {{ tab.label }}
                        </button>
                    </div>
                </Transition>
            </div>
            <div class="w-8"></div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const isMobileView = ref(false)
const isDropdownOpen = ref(false)
const dropdownRef = ref(null)

const dropdownTabs = [
    { key: 'foryou', label: 'For You', icon: 'bx bx-star', path: '/' },
    { key: 'new', label: 'New', icon: 'bx bx-time-five', path: '/feed/new' },
    { key: 'following', label: 'Following', icon: 'bx bx-group', path: '/feed/following' }
]

const activeTab = computed(() => {
    if (route.path === '/feed/following') return 'following'
    if (route.path === '/feed/new') return 'new'
    return 'foryou' // '/' and '/feed/for-you' both = For You
})

const activeLabel = computed(() => {
    const tab = dropdownTabs.find((t) => t.key === activeTab.value)
    return tab ? tab.label : 'For You'
})

const showTabsAndSearch = computed(() => {
    return route.path === '/' || route.path === '/feed/for-you' || route.path === '/feed/new' || route.path === '/feed/following'
})

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const selectTab = (tab) => {
    isDropdownOpen.value = false
    if (route.path !== tab.path) {
        router.push(tab.path)
    }
}

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isDropdownOpen.value = false
    }
}

const checkMobileView = () => {
    isMobileView.value = window.innerWidth < 768
}

watch(route, () => {
    isDropdownOpen.value = false
})

onMounted(() => {
    checkMobileView()
    window.addEventListener('resize', checkMobileView)
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobileView)
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.safe-area-top {
    padding-top: env(safe-area-inset-top);
}

.dropdown-enter-active {
    transition: all 0.2s ease-out;
}
.dropdown-leave-active {
    transition: all 0.15s ease-in;
}
.dropdown-enter-from {
    opacity: 0;
    transform: translate(-50%, -4px) scale(0.95);
}
.dropdown-leave-to {
    opacity: 0;
    transform: translate(-50%, -4px) scale(0.95);
}
</style>
