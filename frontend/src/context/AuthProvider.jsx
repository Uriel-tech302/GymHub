import { useEffect, useMemo, useState } from 'react'
import api from '../api/axios'
import AuthContext from './AuthContext'

/**
 * Recupera de forma segura el usuario almacenado
 * anteriormente en localStorage.
 */
function obtenerUsuarioGuardado() {
    const usuarioGuardado = localStorage.getItem('gymhub_user')

    if (!usuarioGuardado) {
        return null
    }

    try {
        return JSON.parse(usuarioGuardado)
    } catch {
        localStorage.removeItem('gymhub_user')
        return null
    }
}

/**
 * Elimina toda la información de la sesión local.
 */
function limpiarSesionLocal() {
    localStorage.removeItem('gymhub_token')
    localStorage.removeItem('gymhub_user')
}

function AuthProvider({ children }) {
    const tokenGuardado = localStorage.getItem('gymhub_token')

    const [token, setToken] = useState(tokenGuardado)
    const [user, setUser] = useState(obtenerUsuarioGuardado)
    const [loading, setLoading] = useState(Boolean(tokenGuardado))

    /**
     * Comprueba que el token almacenado siga siendo válido.
     * Se ejecuta al abrir o recargar la aplicación.
     */
    useEffect(() => {
        let componenteActivo = true

        const verificarSesion = async () => {
            if (!token) {
                if (componenteActivo) {
                    setLoading(false)
                }

                return
            }

            if (componenteActivo) {
                setLoading(true)
            }

            try {
                const response = await api.get('/user')

                /*
                 * Laravel normalmente devuelve:
                 * {
                 *   message: "...",
                 *   user: {...}
                 * }
                 *
                 * También se acepta directamente response.data
                 * para evitar problemas si cambia el formato.
                 */
                const usuarioAutenticado =
                    response.data.user ?? response.data

                if (!usuarioAutenticado?.id) {
                    throw new Error(
                        'Laravel no devolvió un usuario válido.',
                    )
                }

                if (componenteActivo) {
                    setUser(usuarioAutenticado)

                    localStorage.setItem(
                        'gymhub_user',
                        JSON.stringify(usuarioAutenticado),
                    )
                }
            } catch (error) {
                console.error(
                    'No fue posible verificar la sesión:',
                    error,
                )

                limpiarSesionLocal()

                if (componenteActivo) {
                    setToken(null)
                    setUser(null)
                }
            } finally {
                if (componenteActivo) {
                    setLoading(false)
                }
            }
        }

        verificarSesion()

        return () => {
            componenteActivo = false
        }
    }, [token])

    /**
     * Inicia sesión mediante el endpoint de Laravel.
     */
    const login = async (credentials) => {
        const response = await api.post(
            '/login',
            credentials,
        )

        const nuevoToken = response.data.access_token
        const usuarioAutenticado = response.data.user

        /*
         * Verifica que Laravel realmente haya devuelto
         * tanto el token como el usuario.
         */
        if (!nuevoToken || !usuarioAutenticado?.id) {
            throw new Error(
                'La respuesta del inicio de sesión no es válida.',
            )
        }

        localStorage.setItem(
            'gymhub_token',
            nuevoToken,
        )

        localStorage.setItem(
            'gymhub_user',
            JSON.stringify(usuarioAutenticado),
        )

        setToken(nuevoToken)
        setUser(usuarioAutenticado)
        setLoading(false)

        return usuarioAutenticado
    }
    /**
   * Registra una cuenta nueva.
   *
   * Laravel asignará automáticamente el rol Cliente.
   */
    const register = async (formData) => {
        const response = await api.post(
            '/register',
            formData,
        )

        const nuevoToken = response.data.access_token
        const usuarioRegistrado = response.data.user

        if (!nuevoToken || !usuarioRegistrado?.id) {
            throw new Error(
                'La respuesta del registro no es válida.',
            )
        }

        localStorage.setItem(
            'gymhub_token',
            nuevoToken,
        )

        localStorage.setItem(
            'gymhub_user',
            JSON.stringify(usuarioRegistrado),
        )

        setToken(nuevoToken)
        setUser(usuarioRegistrado)
        setLoading(false)

        return usuarioRegistrado
    }

    /**
     * Cierra la sesión actual.
     */
    const logout = async () => {
        try {
            if (token) {
                await api.post('/logout')
            }
        } catch (error) {
            console.error(
                'No fue posible cerrar la sesión en Laravel:',
                error,
            )
        } finally {
            limpiarSesionLocal()

            setToken(null)
            setUser(null)
            setLoading(false)
        }
    }

    /**
     * Datos y funciones disponibles en toda la aplicación.
     */
    const value = useMemo(
        () => ({
            user,
            token,
            loading,
            isAuthenticated: Boolean(user && token),
            login,
            register,
            logout,
        }),
        [user, token, loading],
    )

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    )
}

export default AuthProvider