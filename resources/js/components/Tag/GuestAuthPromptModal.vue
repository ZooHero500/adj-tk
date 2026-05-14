<template>
    <!-- Simplified: delegates to AuthModal bottom sheet -->
</template>

<script setup>
import { watch, inject } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close'])

const authStore = inject('authStore')

watch(
    () => props.show,
    (val) => {
        if (val) {
            authStore.openAuthModal('login')
            emit('close')
        }
    }
)
</script>
