<template>
    <BlankLayout>
        <div
            v-if="loading"
            class="container mx-auto min-h-screen flex h-full bg-white dark:bg-gray-900 transition-colors"
        >
            <div class="flex flex-1 h-full justify-center items-center">
                <Spinner />
            </div>
        </div>

        <div
            v-else-if="error"
            class="relative min-h-full w-full bg-white dark:bg-neutral-950 px-6 py-24 sm:py-32 lg:px-8 overflow-hidden"
        >
            <div class="absolute inset-0 -z-10">
                <div
                    class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-primary/10 to-purple-500/10 rounded-full blur-3xl"
                ></div>
                <div
                    class="absolute bottom-0 right-1/4 w-64 h-64 bg-gradient-to-r from-blue-500/10 to-primary/10 rounded-full blur-3xl"
                ></div>
            </div>

            <div class="flex min-h-[70vh] flex-col items-center justify-center text-center">
                <div class="mb-8 relative">
                    <div>
                        <ExclamationTriangleIcon class="h-24 w-24 text-primary mx-auto" />
                    </div>
                </div>

                <div class="relative mb-6">
                    <h2
                        class="text-8xl sm:text-9xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent opacity-90 animate-pulse"
                    >
                        404
                    </h2>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent opacity-20 blur-sm text-8xl sm:text-9xl font-bold"
                    >
                        404
                    </div>
                </div>

                <h1
                    class="mt-4 text-balance text-4xl font-bold tracking-tight text-gray-900 dark:text-neutral-50 sm:text-6xl mb-6 animate-fade-in"
                >
                    Notification not found
                </h1>

                <p
                    class="text-pretty text-lg font-medium text-gray-600 dark:text-neutral-400 sm:text-xl max-w-2xl leading-relaxed mb-10 animate-fade-in-delay"
                >
                    This system notification doesn't exist or may have been removed.
                </p>
            </div>
        </div>

        <div
            v-else
            class="container mx-auto min-h-screen bg-white dark:bg-gray-900 transition-colors"
        >
            <header class="relative overflow-hidden pb-12 pt-8">
                <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
                <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div
                        class="flex items-start flex-col justify-center items-center md:flex-row gap-4"
                    >
                        <div class="flex w-full justify-center md:w-auto md:flex-shrink-0">
                            <div
                                class="flex items-center justify-center flex-1 w-full md:w-20 h-20 rounded-2xl"
                                :class="getIconBackgroundClass()"
                            >
                                <component
                                    :is="getSystemIcon()"
                                    class="w-12 h-12"
                                    :class="getIconColorClass()"
                                />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h1
                                class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2"
                            >
                                {{ notification.title }}
                            </h1>
                            <div class="flex gap-4 items-center">
                                <div
                                    class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 inset-ring inset-ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:inset-ring-red-400/20"
                                >
                                    System Notification
                                </div>
                                <div class="flex items-center gap-1 text-gray-500">
                                    <span class="bx bx-time"></span>
                                    <span class="text-sm">
                                        Published {{ formatRecentDate(notification.published_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <section class="pb-20 mb-20 bg-white dark:bg-gray-900">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div
                        class="bg-white dark:bg-gray-800/50 rounded-2xl sm:p-8 md:border border-gray-200 dark:border-gray-700"
                    >
                        <div class="" v-html="formattedBody"></div>
                    </div>
                </div>
            </section>
        </div>
    </BlankLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Spinner from '@/components/Spinner.vue'
import { useHead } from '@unhead/vue'
import { useUtils } from '@/composables/useUtils'
import {
    ExclamationTriangleIcon,
    BellIcon,
    ArrowLeftIcon,
    InformationCircleIcon,
    SparklesIcon,
    MegaphoneIcon,
    ArrowRightIcon
} from '@heroicons/vue/24/outline'
import BlankLayout from '@/layouts/BlankLayout.vue'

const systemTypeConfig = {
    info: {
        icon: InformationCircleIcon,
        iconColor: 'text-white',
        bgColor: 'bg-blue-500'
    },
    update: {
        icon: MegaphoneIcon,
        iconColor: 'text-white',
        bgColor: 'bg-purple-500'
    },
    feature: {
        icon: SparklesIcon,
        iconColor: 'text-white',
        bgColor: 'bg-green-500'
    }
}

const route = useRoute()
const router = useRouter()

const { formatRecentDate } = useUtils()

const loading = ref(true)
const error = ref(false)
const notification = ref({
    id: '',
    title: '',
    body: '',
    link: null
})

const notificationId = computed(() => route.params.id)

const formattedBody = computed(() => {
    if (!notification.value.body) {
        return ''
    }
    return notification.value.body
        .split('\n\n')
        .map((paragraph) => `<p>${paragraph.replace(/\n/g, '<br>')}</p>`)
        .join('')
})

const loadNotification = async () => {
    loading.value = true
    error.value = false

    const baseHeaders = route.query.appreq
        ? {
              'Content-Type': 'application/json',
              Accept: 'application/json',
              'X-LOOPS-APP': true
          }
        : {
              'Content-Type': 'application/json',
              Accept: 'application/json'
          }
    try {
        const response = await fetch(`/api/v1/notifications/system/${notificationId.value}`, {
            method: 'GET',
            headers: baseHeaders
        })

        if (!response.ok) {
            throw new Error('Notification not found')
        }

        const data = await response.json()
        notification.value = data.data
    } catch (err) {
        console.error('Error loading notification:', err)
        error.value = true
    } finally {
        loading.value = false
    }
}

const getSystemIcon = () => {
    const config = systemTypeConfig[notification.value.type]
    return config?.icon || BellIcon
}

const getIconColorClass = () => {
    const config = systemTypeConfig[notification.value.type]
    return config?.iconColor || 'text-white'
}

const getIconBackgroundClass = () => {
    const config = systemTypeConfig[notification.value.type]
    return config?.bgColor || 'bg-gray-500'
}

onMounted(() => {
    loadNotification()
})
</script>
