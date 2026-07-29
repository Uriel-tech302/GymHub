import useAuth from '../../hooks/useAuth'

function HomePage() {
  const {
    user,
    logout,
  } = useAuth()

  const handleLogout = async () => {
    await logout()
  }

  return (
    <main>
      <h1>Bienvenido a GymHub</h1>

      <p>
        <strong>Nombre:</strong> {user?.name}
      </p>

      <p>
        <strong>Correo:</strong> {user?.email}
      </p>

      <p>
        <strong>Rol:</strong> {user?.role}
      </p>

      <button
        type="button"
        onClick={handleLogout}
      >
        Cerrar sesión
      </button>
    </main>
  )
}

export default HomePage