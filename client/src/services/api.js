import axios from 'axios'
import { useAuthStore } from '../stores/authStore.js'

const API_BASE_URL = import.meta.env.VITE_API_URL || (import.meta.env.PROD ? '/api' : 'http://localhost:5000/api')

const api = axios.create({
  baseURL: API_BASE_URL
})

// Add token to requests
api.interceptors.request.use((config) => {
  const authStore = useAuthStore()
  if (authStore.token) {
    config.headers.Authorization = `Bearer ${authStore.token}`
  }
  return config
})

export default api
