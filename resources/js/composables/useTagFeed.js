import { useInfiniteQuery } from '@tanstack/vue-query'
import { computed } from 'vue'
import { fetchTagFeedPage } from '~/api/tagFeed'

export const useTagFeed = (tag) => {
    return useInfiniteQuery({
        queryKey: computed(() => ['tagFeed', tag.value]),
        queryFn: ({ pageParam }) =>
            fetchTagFeedPage({ tag: tag.value, cursor: pageParam }),
        getNextPageParam: (lastPage) => lastPage.meta?.next_cursor ?? undefined,
        initialPageParam: null,
        enabled: computed(() => !!tag.value),
        staleTime: 1000 * 60 * 5,
        retry: 2
    })
}
