import {
  BadgeCheck,
  Mail,
  ShieldCheck,
} from 'lucide-react'
import useAuth from '../../hooks/useAuth'
import './HomePage.css'

function HomePage() {
  const { user } = useAuth()

  return (
    <main className="home-page">
      <section className="home-welcome">
        <div>
          <span className="home-label">
            Panel principal
          </span>

          <h1>
            Hola, {user?.name}
          </h1>

          <p>
            Bienvenido al sistema de gestión de
            GymHub.
          </p>
        </div>

        <div className="home-status">
          <BadgeCheck size={20} />
          Sesión activa
        </div>
      </section>

      <section className="home-information-grid">
        <article className="home-information-card">
          <div className="home-card-icon">
            <ShieldCheck size={24} />
          </div>

          <div>
            <span>Rol asignado</span>
            <strong>{user?.role}</strong>
          </div>
        </article>

        <article className="home-information-card">
          <div className="home-card-icon">
            <Mail size={24} />
          </div>

          <div>
            <span>Correo electrónico</span>
            <strong>{user?.email}</strong>
          </div>
        </article>

        <article className="home-information-card">
          <div className="home-card-icon">
            <BadgeCheck size={24} />
          </div>

          <div>
            <span>Estado de la cuenta</span>
            <strong>Activa</strong>
          </div>
        </article>
      </section>

      <section className="home-placeholder">
        <h2>Resumen de GymHub</h2>

        <p>
          Los indicadores de ventas, membresías,
          inventario y vencimientos se agregarán
          en el siguiente paso.
        </p>
      </section>
    </main>
  )
}

export default HomePage