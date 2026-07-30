import useAuth from '../../hooks/useAuth'
import AdminHome from '../dashboard/AdminHome'
import EmployeeHome from '../dashboard/EmployeeHome'
import ClientHome from '../dashboard/ClientHome'

function HomePage() {
  const { user } = useAuth()

  if (!user) {
    return null
  }

  switch (user.role) {
    case 'Administrador':
      return <AdminHome user={user} />

    case 'Empleado':
      return <EmployeeHome user={user} />

    case 'Cliente':
      return <ClientHome user={user} />

    default:
      return (
        <section>
          <h1>Rol no reconocido</h1>
          <p>
            No fue posible determinar el panel correspondiente.
          </p>
        </section>
      )
  }
}

export default HomePage