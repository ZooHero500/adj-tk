<template>
    <div
        class="min-h-screen bg-[#FAFAFA] dark:bg-[#0A0A0A] flex items-center justify-center px-6 font-body"
    >
        <div class="text-center max-w-sm">
            <div
                class="w-16 h-16 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900/50 flex items-center justify-center mx-auto mb-5"
            >
                <svg
                    class="w-7 h-7 text-red-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                    />
                </svg>
            </div>

            <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ displayTitle }}
            </h2>

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                {{ displayMessage }}
            </p>

            <div class="flex gap-3 justify-center">
                <button
                    v-if="!isNotFound"
                    @click="handleRetry"
                    class="px-5 py-2.5 bg-primary hover:bg-[#D91B42] text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-primary/25"
                >
                    {{ $t('common.tryAgain') }}
                </button>

                <slot name="action">
                    <router-link
                        to="/starter-kits"
                        class="px-5 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-xl transition-all"
                    >
                        {{ $t('common.browseKits') }}
                    </router-link>
                </slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    error: {
        type: [String, Boolean, Object],
        default: false
    },
    title: String,
    message: String
})

const emit = defineEmits(['retry'])

const isNotFound = computed(() => props.error === 'not_found')
const isForbidden = computed(() => props.error === 'forbidden')

const displayTitle = computed(() => {
    if (props.title) return props.title
    if (isForbidden.value) {
        return 'Access Denied'
    }
    return isNotFound.value ? 'Kit not found' : 'Something went wrong'
})

const displayMessage = computed(() => {
    if (props.message) return props.message
    if (isForbidden.value) {
        return "You don't have permission to edit this starter kit."
    }
    return isNotFound.value
        ? "This starter kit doesn't exist or may have been removed."
        : "We couldn't load this starter kit. Please try again."
})

const handleRetry = () => {
    emit('retry')
}
</script>
