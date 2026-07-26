import axios from 'axios';

// Instancia global de Axios
const api = axios.create({
  baseURL: 'http://localhost:8000/api', // Cambia la URL por la de tu backend/VPS si es distinta
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor para enviar el Token de autenticación si existe
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Interceptor para manejo global de errores de red
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (!error.response) {
      console.error('Error de red: No se pudo conectar con el servidor.');
    } else if (error.response.status === 401) {
      // Token expirado o no autorizado
      localStorage.removeItem('token');
    }
    return Promise.reject(error);
  }
);

export default api;