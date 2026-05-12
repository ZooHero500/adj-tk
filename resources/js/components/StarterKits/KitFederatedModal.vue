<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="modelValue"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 font-body"
            >
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close" />

                <div
                    class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden"
                >
                    <div
                        class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800"
                    >
                        <div class="flex items-center gap-2.5">
                            <div
                                class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0"
                            >
                                <GlobeAltIcon class="w-4 h-4 text-primary" />
                            </div>
                            <h3 class="font-display font-bold text-gray-900 dark:text-white">
                                Use from your platform
                            </h3>
                        </div>
                        <button
                            @click="close"
                            class="p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 transition-colors"
                        >
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="p-5 space-y-5">
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                            This starter kit supports
                            <span class="font-semibold text-gray-900 dark:text-white"
                                >federated use</span
                            >
                            — you can apply it directly from your own Fediverse account on any
                            compatible platform.
                        </p>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2"
                            >
                                Starter Kit URL
                            </label>
                            <div class="flex gap-2">
                                <input
                                    :value="starterKit.url"
                                    readonly
                                    class="flex-1 min-w-0 px-3.5 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-600 dark:text-gray-300 font-mono select-all focus:outline-none cursor-text"
                                    @click="(e) => e.target.select()"
                                />
                                <button
                                    @click="copyFederatedUrl"
                                    class="flex-shrink-0 flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-all"
                                    :class="
                                        copiedFederated
                                            ? 'bg-emerald-500 text-white'
                                            : 'bg-primary hover:bg-[#D91B42] text-white shadow-md shadow-primary/25'
                                    "
                                >
                                    <CheckCircleIcon v-if="copiedFederated" class="w-4 h-4" />
                                    <ClipboardDocumentIcon v-else class="w-4 h-4" />
                                    {{ copiedFederated ? 'Copied!' : 'Copy' }}
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 space-y-3">
                            <p
                                class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                How to use it
                            </p>
                            <div class="space-y-2.5">
                                <div
                                    v-for="(step, i) in [
                                        'Copy the kit URL above',
                                        'Open your Fediverse app and paste the URL into the search bar',
                                        'If your platform supports starter kits, you\'ll be able to follow all accounts with one tap'
                                    ]"
                                    :key="i"
                                    class="flex items-start gap-3"
                                >
                                    <span
                                        class="w-5 h-5 rounded-full bg-primary/10 text-primary text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5"
                                    >
                                        {{ i + 1 }}
                                    </span>
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed"
                                    >
                                        {{ step }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed">
                            Not sure if your app is compatible? Search the URL in your platform's
                            search bar to test. If it loads this kit, you're good to go. PornTk and
                            other ActivityPub platforms that support this feature will recognise it
                            automatically.
                        </p>
                    </div>

                    <div class="px-5 pb-5 flex gap-3">
                        <button
                            @click="close"
                            class="flex-1 py-2.5 text-sm font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-gray-300 transition-all"
                        >
                            Close
                        </button>
                        <AnimatedButton @click="handleJoin" class="flex-1">
                            Join PornTk
                        </AnimatedButton>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, inject } from 'vue'
import {
    GlobeAltIcon,
    XMarkIcon,
    CheckCircleIcon,
    ClipboardDocumentIcon
} from '@heroicons/vue/24/outline'
import AnimatedButton from '../AnimatedButton.vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    starterKit: {
        type: Object,
        required: true
    }
})

const authStore = inject('authStore')

const emit = defineEmits(['update:modelValue'])

const copiedFederated = ref(false)

function close() {
    emit('update:modelValue', false)
}

const handleJoin = async () => {
    close()
    authStore.openAuthModal('register', props.starterKit.url)
}

async function copyFederatedUrl() {
    try {
        await navigator.clipboard.writeText(props.starterKit.url)
        copiedFederated.value = true
        setTimeout(() => (copiedFederated.value = false), 2000)
    } catch {
        const el = document.createElement('textarea')
        el.value = props.starterKit.url
        document.body.appendChild(el)
        el.select()
        document.execCommand('copy')
        document.body.removeChild(el)
        copiedFederated.value = true
        setTimeout(() => (copiedFederated.value = false), 2000)
    }
}
</script>
