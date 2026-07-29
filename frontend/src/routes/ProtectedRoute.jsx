import { Navigate } from 'react-router-dom'
import useAuth from '../hooks/useAuth'

/**
 * Protege las páginas que requieren autenticación.
 * También permite restringirlas según el rol del usuario.
 */
function ProtectedRoute({
  children,
  allowedRoles = [],
}) {
  const {
    user,
    loading,
    isAuthenticated,
  } = useAuth()

  if (loading) {
    return (
      <main>
        <h2>Verificando sesión...</h2>
      </main>
    )
  }

  if (!isAuthenticated) {
    return (
      <Navigate
        to="/login"
        replace
      />
    )
  }

  if (
    allowedRoles.length > 0 &&
    !allowedRoles.includes(user.role)
  ) {
    return (
      <Navigate
        to="/inicio"
        replace
      />
    )
  }

  return children
}

export default ProtectedRoute