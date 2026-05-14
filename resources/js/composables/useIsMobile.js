import { ref, onMounted, onUnmounted } from 'vue'

export function useIsMobile(breakpoint = 767) {
    const query = `(max-width: ${breakpoint}px)`
    const mediaQuery = typeof window !== 'undefined' ? window.matchMedia(query) : null
    const isMobile = ref(mediaQuery ? mediaQuery.matches : false)

    const handler = (e) => {
        isMobile.value = e.matches
    }

    onMounted(() => {
        if (mediaQuery) {
            mediaQuery.addEventListener('change', handler)
        }
    })

    onUnmounted(() => {
        if (mediaQuery) {
            mediaQuery.removeEventListener('change', handler)
        }
    })

    return { isMobile }
}
