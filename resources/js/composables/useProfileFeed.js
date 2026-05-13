import { useInfiniteQuery } from '@tanstack/vue-query'
import { computed } from 'vue'
import { fetchProfileFeedPage } from '~/api/profileFeed'

export const useProfileFeed = (accountId) => {
    return useInfiniteQuery({
        queryKey: computed(() => ['profileFeed', accountId.value]),
        queryFn: ({ pageParam }) =>
            fetchProfileFeedPage({ accountId: accountId.value, cursor: pageParam }),
        getNextPageParam: (lastPage) => lastPage.meta?.next_cursor ?? undefined,
        initialPageParam: null,
        enabled: computed(() => !!accountId.value),
        staleTime: 1000 * 60 * 5,
        retry: 2
    })
}
