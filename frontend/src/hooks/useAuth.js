import { useContext } from 'react'
import AuthContext from '../context/AuthContext'

/**
 * Hook personalizado para acceder fácilmente
 * a la sesión del usuario desde cualquier componente.
 */
function useAuth() {
  const context = useContext(AuthContext)

  if (!context) {
    throw new Error(
      'useAuth debe utilizarse dentro de AuthProvider.',
    )
  }

  return context
}

export default useAuth