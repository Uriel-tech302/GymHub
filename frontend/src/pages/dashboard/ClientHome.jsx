import {
  CalendarDays,
  Dumbbell,
  History,
  ShoppingBag,
  TrendingUp,
  WalletCards,
} from 'lucide-react'
import './DashboardHome.css'

function ClientHome({ user }) {
  return (
    <section className="dashboard-home">
      <header className="dashboard-welcome">
        <div>
          <p className="dashboard-eyebrow">
            Mi cuenta
          </p>

          <h1>
            Hola, {user.name}
          </h1>

          <p>
            Consulta el estado de tu membresía, tus rutinas
            disponibles y el historial de tu cuenta.
          </p>
        </div>

        <span className="dashboard-status">
          Cliente
        </span>
      </header>

      <div className="dashboard-grid">
        <article className="dashboard-card">
          <div className="dashboard-card-icon">
            <WalletCards size={28} />
          </div>

          <div>
            <span>Tipo de membresía</span>
            <strong>Mensual</strong>
          </div>

          <button type="button">
            Ver membresía
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon success">
            <CalendarDays size={28} />
          </div>

          <div>
            <span>Días restantes</span>
            <strong>1</strong>
          </div>

          <button type="button">
            Ver vigencia
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon info">
            <Dumbbell size={28} />
          </div>

          <div>
            <span>Rutinas disponibles</span>
            <strong>8</strong>
          </div>

          <button type="button">
            Ver rutinas
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon purple">
            <ShoppingBag size={28} />
          </div>

          <div>
            <span>Compras realizadas</span>
            <strong>3</strong>
          </div>

          <button type="button">
            Ver historial
          </button>
        </article>
      </div>

      <section className="dashboard-chart-card">
        <div className="dashboard-chart-header">
          <div>
            <p className="dashboard-eyebrow">
              Actividad reciente
            </p>

            <h2>
              Asistencia semanal
            </h2>
          </div>

          <TrendingUp size={24} />
        </div>

        <div className="dashboard-bars">
          <div className="dashboard-bar-item">
            <span>Lun</span>
            <div style={{ height: '65%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Mar</span>
            <div style={{ height: '35%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Mié</span>
            <div style={{ height: '80%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Jue</span>
            <div style={{ height: '45%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Vie</span>
            <div style={{ height: '90%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Sáb</span>
            <div style={{ height: '70%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Dom</span>
            <div style={{ height: '25%' }} />
          </div>
        </div>

        <div className="dashboard-client-summary">
          <History size={20} />

          <p>
            Tu actividad más reciente quedará registrada
            en el historial de GymHub.
          </p>
        </div>
      </section>
    </section>
  )
}

export default ClientHome