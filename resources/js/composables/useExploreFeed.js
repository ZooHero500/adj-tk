import { useInfiniteQuery } from '@tanstack/vue-query'
import { computed } from 'vue'
import { fetchExploreFeedPage } from '~/api/exploreFeed'

export const useExploreFeed = (tag) => {
    return useInfiniteQuery({
        queryKey: computed(() => ['exploreFeed', tag.value]),
        queryFn: ({ pageParam }) =>
            fetchExploreFeedPage({ tag: tag.value, cursor: pageParam }),
        getNextPageParam: (lastPage) => lastPage.meta?.next_cursor ?? undefined,
        initialPageParam: null,
        enabled: computed(() => !!tag.value),
        staleTime: 1000 * 60 * 5,
        retry: 2
    })
}
