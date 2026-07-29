import axios from 'axios'

/**
 * Instancia central para comunicarse con la API de Laravel.
 */
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 15000,
})

/**
 * Agrega automáticamente el token de Sanctum
 * a todas las peticiones protegidas.
 */
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('gymhub_token')

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    return config
  },
  (error) => Promise.reject(error),
)

export default api