import axios from '~/plugins/axios'

export const fetchSearchFeedPage = async ({ query, cursor = null }) => {
    const axiosInstance = axios.getAxiosInstance()
    const response = await axiosInstance.get('/api/v1/search', {
        params: {
            query,
            type: 'videos',
            per_page: 10,
            ...(cursor ? { cursor } : {})
        }
    })
    // Normalize: search API nests videos under data.data.videos
    return {
        data: response.data.data?.videos || [],
        meta: response.data.meta || {}
    }
}
