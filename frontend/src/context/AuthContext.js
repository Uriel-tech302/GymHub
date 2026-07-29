import { createContext } from 'react'

/**
 * Contexto global que almacenará la información
 * de la sesión activa de GymHub.
 */
const AuthContext = createContext(null)

export default AuthContext