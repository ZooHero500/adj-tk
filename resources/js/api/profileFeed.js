import axios from '~/plugins/axios'

export const fetchProfileFeedPage = async ({ accountId, cursor = null }) => {
    const axiosInstance = axios.getAxiosInstance()
    const response = await axiosInstance.get(`/api/v1/feed/account/${accountId}`, {
        params: cursor ? { cursor } : {}
    })
    return response.data
}
