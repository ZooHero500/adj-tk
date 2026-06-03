import { useInfiniteQuery } from '@tanstack/vue-query'
import { computed } from 'vue'
import { fetchSearchFeedPage } from '~/api/searchFeed'

export const useSearchFeed = (query) => {
    return useInfiniteQuery({
        queryKey: computed(() => ['searchFeed', query.value]),
        queryFn: ({ pageParam }) =>
            fetchSearchFeedPage({ query: query.value, cursor: pageParam }),
        getNextPageParam: (lastPage) => lastPage.meta?.next_cursor ?? undefined,
        initialPageParam: null,
        enabled: computed(() => !!query.value),
        staleTime: 1000 * 60 * 5,
        retry: 2
    })
}
