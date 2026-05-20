import axios, { AxiosError } from 'axios'
import toast from 'react-hot-toast'
import { useAuthStore } from '@/stores/authStore'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
})

api.interceptors.request.use((config) => {
  const token = useAuthStore.getState().token
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  (res) => res,
  (error: AxiosError<{ message?: string; errors?: Record<string, string[]> }>) => {
    const message = error.response?.data?.message || 'Error en la solicitud'
    if (error.response?.status === 401) {
      useAuthStore.getState().logout()
      window.location.href = '/login'
    } else if (error.response?.status === 422) {
      const errors = error.response.data?.errors
      const first = errors ? Object.values(errors)[0]?.[0] : message
      toast.error(first || message)
    } else {
      toast.error(message)
    }
    return Promise.reject(error)
  }
)

export default api

export interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
}

export interface Paginated<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
