import axios from '~/plugins/axios'

export const fetchTagFeedPage = async ({ tag, cursor = null }) => {
    const axiosInstance = axios.getAxiosInstance()
    const response = await axiosInstance.get(`/api/v1/tags/video/${tag}`, {
        params: cursor ? { cursor } : {}
    })
    return response.data
}
