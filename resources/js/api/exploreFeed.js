import axios from '~/plugins/axios'

export const fetchExploreFeedPage = async ({ tag, cursor = null }) => {
    const axiosInstance = axios.getAxiosInstance()
    const response = await axiosInstance.get(`/api/v1/explore/tag-feed/${tag}`, {
        params: cursor ? { cursor } : {}
    })
    return response.data
}
