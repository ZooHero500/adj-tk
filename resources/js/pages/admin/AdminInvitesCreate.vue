<template>
    <div>
        <div class="mb-6">
            <div class="mb-8">
                <div class="mb-3">
                    <router-link
                        to="/admin/invites"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                    >
                        ← Back to invites
                    </router-link>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Invite Link</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Generate a new invite link for users to join PornTk
                </p>
            </div>
            <form
                @submit.prevent
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6 space-y-6 flex justify-center flex-col lg:flex-row lg:gap-10">
                    <div class="flex-1 space-y-6">
                        <div>
                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-2"
                            >
                                Title
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                placeholder="Add a title for internal use"
                                :maxlength="80"
                                class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            />
                            <div class="mt-1 flex justify-between items-center gap-5">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Only visible to admins - use this for easy ID reference
                                </p>
                                <div class="text-xs font-mono text-gray-400 dark:text-gray-500">
                                    {{ form.title?.length || 0 }}/80
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                for="message"
                                class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-2"
                            >
                                Invite Message
                            </label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="3"
                                placeholder="Optional welcome message for invited users..."
                                class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            ></textarea>
                            <div class="mt-0 flex justify-between items-start gap-5">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    This message will be shown to users when they use the invite
                                    link
                                </p>
                                <div class="text-xs font-mono text-gray-400 dark:text-gray-500">
                                    {{ form.message?.length || 0 }}/500
                                </div>
                            </div>
                        </div>
                        <div>
                            <label
                                for="admin_note"
                                class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-2"
                            >
                                Admin Note
                            </label>
                            <textarea
                                id="admin_note"
                                v-model="form.admin_note"
                                rows="6"
                                placeholder="Internal note (not visible to users)..."
                                class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            ></textarea>
                            <div class="mt-0 flex justify-between items-start gap-5">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Only visible to admins - use this for tracking invite purpose
                                </p>
                                <div class="text-xs font-mono text-gray-400 dark:text-gray-500">
                                    {{ form.admin_note?.length || 0 }}/2000
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 space-y-6">
                        <div class="relative">
                            <label
                                class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-2"
                            >
                                Auto-follow Accounts ({{ selectedAccounts.length }}/5)
                            </label>

                            <div
                                class="w-full min-h-[42px] px-2 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent transition flex flex-wrap items-center gap-2 cursor-text"
                                @click="$refs.searchInput.focus()"
                            >
                                <div
                                    v-for="account in selectedAccounts"
                                    :key="account.id"
                                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800"
                                >
                                    <img
                                        :src="account.avatar"
                                        class="w-4 h-4 rounded-full bg-gray-200"
                                        alt=""
                                    />
                                    <span
                                        class="text-xs font-medium text-blue-700 dark:text-blue-200"
                                        >@{{ account.username }}</span
                                    >
                                    <button
                                        type="button"
                                        @click.stop="removeAccount(account.id)"
                                        class="ml-0.5 text-blue-400 hover:text-blue-600 dark:hover:text-blue-100 focus:outline-none"
                                    >
                                        <svg
                                            class="w-3 h-3"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>

                                <input
                                    ref="searchInput"
                                    v-model="searchQuery"
                                    @input="handleSearchInput"
                                    @keydown.backspace="handleBackspace"
                                    @focus="showResults = true"
                                    :disabled="selectedAccounts.length >= 5"
                                    :placeholder="
                                        selectedAccounts.length === 0
                                            ? 'Search users (@username)...'
                                            : selectedAccounts.length < 5
                                              ? 'Add another...'
                                              : 'Limit reached'
                                    "
                                    class="flex-1 min-w-[120px] bg-transparent border-none outline-none text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50"
                                />
                            </div>

                            <div
                                v-if="showResults && (searchResults.length > 0 || isSearching)"
                                class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto"
                            >
                                <div
                                    v-if="isSearching"
                                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2"
                                >
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                            fill="none"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    Searching...
                                </div>

                                <ul v-else class="py-1">
                                    <li v-for="result in searchResults" :key="result.id">
                                        <button
                                            type="button"
                                            @click="selectAccount(result)"
                                            class="w-full px-4 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-3 transition-colors"
                                        >
                                            <img
                                                :src="result.avatar"
                                                class="w-8 h-8 rounded-full bg-gray-200 object-cover"
                                                alt=""
                                            />
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >
                                                    {{ result.name || result.username }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    @{{ result.username }}
                                                </div>
                                            </div>
                                            <span
                                                v-if="isSelected(result.id)"
                                                class="ml-auto text-xs font-medium text-blue-600 dark:text-blue-400"
                                            >
                                                Selected
                                            </span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div
                                v-if="showResults"
                                @click="showResults = false"
                                class="fixed inset-0 z-0 cursor-default"
                            ></div>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Search and select up to 5 accounts invited users will automatically
                                follow.
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    for="max_uses"
                                    class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-2"
                                >
                                    Max Uses
                                </label>
                                <input
                                    id="max_uses"
                                    v-model.number="form.max_uses"
                                    type="number"
                                    min="0"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    0 = unlimited uses
                                </p>
                            </div>
                            <div>
                                <label
                                    for="expires_at"
                                    class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-2"
                                >
                                    Expires At
                                </label>
                                <input
                                    id="expires_at"
                                    v-model="form.expires_at"
                                    type="datetime-local"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Leave empty for no expiration
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <label
                                        class="text-sm font-medium text-gray-900 dark:text-gray-300"
                                    >
                                        Require Email Verification
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Users must verify their email before accessing their account
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="form.verify_email = !form.verify_email"
                                    :class="[
                                        form.verify_email
                                            ? 'bg-blue-600'
                                            : 'bg-gray-200 dark:bg-gray-700',
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                                    ]"
                                >
                                    <span
                                        :class="[
                                            form.verify_email ? 'translate-x-5' : 'translate-x-0',
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5'
                                        ]"
                                    />
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <label
                                        class="text-sm font-medium text-gray-900 dark:text-gray-300"
                                    >
                                        Notify Admins
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Send an email notification to admins when a new user joins
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="form.email_admin_on_join = !form.email_admin_on_join"
                                    :class="[
                                        form.email_admin_on_join
                                            ? 'bg-blue-600'
                                            : 'bg-gray-200 dark:bg-gray-700',
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                                    ]"
                                >
                                    <span
                                        :class="[
                                            form.email_admin_on_join
                                                ? 'translate-x-5'
                                                : 'translate-x-0',
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5'
                                        ]"
                                    />
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <label
                                        class="text-sm font-medium text-gray-900 dark:text-gray-300"
                                    >
                                        Active
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Enable this invite link immediately
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="form.is_active = !form.is_active"
                                    :class="[
                                        form.is_active
                                            ? 'bg-blue-600'
                                            : 'bg-gray-200 dark:bg-gray-700',
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                                    ]"
                                >
                                    <span
                                        :class="[
                                            form.is_active ? 'translate-x-5' : 'translate-x-0',
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5'
                                        ]"
                                    />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="error"
                    class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mx-6 mb-6"
                >
                    <div class="flex">
                        <svg
                            class="h-5 w-5 text-red-400 dark:text-red-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-red-800 dark:text-red-200">{{ error }}</p>
                        </div>
                    </div>
                </div>

                <div
                    v-if="success && inviteUrl"
                    class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mx-6 mb-6"
                >
                    <div class="flex items-start">
                        <svg
                            class="h-5 w-5 text-green-400 dark:text-green-500 mt-0.5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200 mb-2">
                                Invite link created successfully!
                            </p>
                            <div
                                class="mt-2 bg-white dark:bg-gray-800 rounded border border-green-300 dark:border-green-700 p-3"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <code
                                        class="text-sm text-gray-900 dark:text-gray-100 flex-1 break-all"
                                        >{{ inviteUrl }}</code
                                    >
                                    <button
                                        @click="copyToClipboard"
                                        type="button"
                                        class="flex-shrink-0 px-3 py-1.5 text-xs font-medium text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/50 hover:bg-green-200 dark:hover:bg-green-900/70 rounded transition"
                                    >
                                        {{ copied ? 'Copied!' : 'Copy' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 rounded-b-lg flex items-center justify-between"
                >
                    <button
                        type="button"
                        @click="resetForm"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition cursor-pointer"
                    >
                        Reset
                    </button>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                        >
                            Cancel
                        </button>
                        <button
                            @click="handleSubmit"
                            type="button"
                            :disabled="loading || !form.title"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            <span v-if="loading" class="flex items-center gap-2"> </span>
                            <span v-else>Create Invite</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { invitesApi } from '@/services/adminApi'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const error = ref(null)
const success = ref(false)
const inviteUrl = ref(null)
const copied = ref(false)

const searchQuery = ref('')
const searchResults = ref([])
const selectedAccounts = ref([])
const isSearching = ref(false)
const showResults = ref(false)
let debounceTimer = null

const form = reactive({
    title: '',
    message: '',
    admin_note: '',
    autofollow_accounts: [],
    verify_email: false,
    email_admin_on_join: false,
    max_uses: 0,
    is_active: true,
    expires_at: ''
})

const handleSearchInput = () => {
    if (!searchQuery.value) {
        searchResults.value = []
        isSearching.value = false
        return
    }

    isSearching.value = true
    showResults.value = true

    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(async () => {
        try {
            const response = await invitesApi.searchProfiles(searchQuery.value)
            searchResults.value = response.data.filter((u) => !selectedAccounts.value.includes(u))
        } catch (e) {
            console.error(e)
        } finally {
            isSearching.value = false
        }
    }, 400)
}

const selectAccount = (account) => {
    if (selectedAccounts.value.length >= 5) return
    if (!isSelected(account.id)) {
        selectedAccounts.value.push(account)
    }
    searchQuery.value = ''
    searchResults.value = []
    showResults.value = false
}

const removeAccount = (id) => {
    selectedAccounts.value = selectedAccounts.value.filter((a) => a.id !== id)
}

const handleBackspace = (e) => {
    if (searchQuery.value === '' && selectedAccounts.value.length > 0) {
        selectedAccounts.value.pop()
    }
}

const isSelected = (id) => {
    return selectedAccounts.value.some((a) => a.id === id)
}

const handleSubmit = async () => {
    loading.value = true
    error.value = null
    success.value = false
    inviteUrl.value = null

    form.autofollow_accounts = selectedAccounts.value.map((a) => a.id)

    try {
        const response = await invitesApi.createInvite(form)
        success.value = true
        router.push('/admin/invites/' + response.data.id)
    } catch (err) {
        error.value = err.message || 'Failed to create invite link'
    } finally {
        loading.value = false
    }
}

const resetForm = () => {
    form.title = ''
    form.message = ''
    form.admin_note = ''
    form.autofollow_accounts = []
    form.email_admin_on_join = false
    form.verify_email = false
    form.max_uses = 0
    form.is_active = true
    form.expires_at = ''

    selectedAccounts.value = []
    searchQuery.value = ''
    searchResults.value = []

    error.value = null
    success.value = false
    inviteUrl.value = null
}

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(inviteUrl.value)
        copied.value = true
        setTimeout(() => {
            copied.value = false
        }, 2000)
    } catch (err) {
        console.error('Failed to copy:', err)
    }
}
</script>
