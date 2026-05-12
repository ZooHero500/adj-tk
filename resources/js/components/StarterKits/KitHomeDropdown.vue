<template>
    <div class="flex items-center gap-3 flex-shrink-0">
        <AnimatedButton
            variant="primaryGradient"
            :pill="true"
            @click="router.push('/starter-kits/browse')"
        >
            <div class="flex items-center gap-2">
                <GlobeAltIcon class="w-4 h-4" />
                {{ t('common.browseAll') }}
            </div>
        </AnimatedButton>

        <div ref="dropdownRef" class="relative">
            <AnimatedButton
                variant="light"
                :pill="true"
                :aria-expanded="dropdownOpen"
                aria-haspopup="menu"
                aria-controls="starter-kit-menu"
                @click="toggleDropdown"
            >
                <div class="flex items-center gap-2">
                    <Bars3Icon class="w-4 h-4" />
                    <span class="inline">{{ t('nav.more') }}</span>
                    <ChevronDownIcon
                        class="w-3.5 h-3.5 transition-transform duration-200"
                        :class="{ 'rotate-180': dropdownOpen }"
                    />
                </div>
            </AnimatedButton>

            <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 scale-95 -translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 -translate-y-1"
            >
                <div
                    v-if="dropdownOpen"
                    id="starter-kit-menu"
                    role="menu"
                    aria-label="Starter kit options"
                    class="absolute right-0 mt-2 w-52 z-50 origin-top-right rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700/60 shadow-xl shadow-black/10 dark:shadow-black/40"
                >
                    <router-link
                        to="/starter-kits/create"
                        role="menuitem"
                        class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors duration-150 cursor-pointer focus:outline-none focus-visible:bg-zinc-100 dark:focus-visible:bg-zinc-800"
                        @click="closeDropdown"
                    >
                        <PlusIcon class="w-4 h-4 text-primary flex-shrink-0" />
                        <span>{{ t('common.createKit') }}</span>
                    </router-link>

                    <div class="border-t border-zinc-100 dark:border-zinc-800" />

                    <router-link
                        to="/starter-kits/my-kits"
                        role="menuitem"
                        class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors duration-150 cursor-pointer focus:outline-none focus-visible:bg-zinc-100 dark:focus-visible:bg-zinc-800"
                        @click="closeDropdown"
                    >
                        <ArchiveBoxIcon class="w-4 h-4 text-primary flex-shrink-0" />
                        {{ t('common.myKits') }}
                    </router-link>

                    <router-link
                        to="/starter-kits/joined-kits"
                        role="menuitem"
                        class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors duration-150 cursor-pointer focus:outline-none focus-visible:bg-zinc-100 dark:focus-visible:bg-zinc-800"
                        @click="closeDropdown"
                    >
                        <UserPlusIcon class="w-4 h-4 text-primary flex-shrink-0" />
                        {{ t('common.joinedKits') }}
                    </router-link>

                    <div class="border-t border-zinc-100 dark:border-zinc-800" />

                    <router-link
                        to="/starter-kits/about"
                        role="menuitem"
                        class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors duration-150 cursor-pointer focus:outline-none focus-visible:bg-zinc-100 dark:focus-visible:bg-zinc-800"
                        @click="closeDropdown"
                    >
                        <InformationCircleIcon class="w-4 h-4 text-primary flex-shrink-0" />
                        {{ t('nav.about') }}
                    </router-link>

                    <router-link
                        to="/starter-kits/faq"
                        role="menuitem"
                        class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors duration-150 cursor-pointer focus:outline-none focus-visible:bg-zinc-100 dark:focus-visible:bg-zinc-800"
                        @click="closeDropdown"
                    >
                        <ListBulletIcon class="w-4 h-4 text-primary flex-shrink-0" />
                        {{ t('common.faq') }}
                    </router-link>

                    <div class="border-t border-zinc-100 dark:border-zinc-800" />

                    <router-link
                        to="/dashboard/privacy"
                        role="menuitem"
                        class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors duration-150 cursor-pointer focus:outline-none focus-visible:bg-zinc-100 dark:focus-visible:bg-zinc-800"
                        @click="closeDropdown"
                    >
                        <Cog6ToothIcon class="w-4 h-4 text-primary flex-shrink-0" />
                        Starter Kit Settings
                    </router-link>
                </div>
            </Transition>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
    GlobeAltIcon,
    PlusIcon,
    ChevronDownIcon,
    Bars3Icon,
    Cog6ToothIcon,
    InformationCircleIcon,
    ListBulletIcon,
    UserPlusIcon,
    ArchiveBoxIcon
} from '@heroicons/vue/24/outline'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

const router = useRouter()
const { t } = useI18n()
const dropdownOpen = ref(false)
const dropdownRef = ref(null)

function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value
}

function closeDropdown() {
    dropdownOpen.value = false
}

function handleKeydown(e) {
    if (e.key === 'Escape') closeDropdown()
}

function handleClickOutside(e) {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        closeDropdown()
    }
}

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside)
    document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside)
    document.removeEventListener('keydown', handleKeydown)
})
</script>
