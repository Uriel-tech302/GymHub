import {
  AlertTriangle,
  Boxes,
  DollarSign,
  TrendingUp,
  UsersRound,
} from 'lucide-react'
import './DashboardHome.css'

function AdminHome({ user }) {
  return (
    <section className="dashboard-home">
      <header className="dashboard-welcome">
        <div>
          <p className="dashboard-eyebrow">
            Panel administrativo
          </p>

          <h1>
            Hola, {user.name}
          </h1>

          <p>
            Consulta el estado general de GymHub y accede
            rápidamente a las áreas principales.
          </p>
        </div>

        <span className="dashboard-status">
          Sesión activa
        </span>
      </header>

      <div className="dashboard-grid">
        <article className="dashboard-card">
          <div className="dashboard-card-icon">
            <DollarSign size={28} />
          </div>

          <div>
            <span>Corte del día</span>
            <strong>$1,540.00</strong>
          </div>

          <button type="button">
            Ver detalles
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon">
            <UsersRound size={28} />
          </div>

          <div>
            <span>Membresías activas</span>
            <strong>1</strong>
          </div>

          <button type="button">
            Ver detalles
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon warning">
            <AlertTriangle size={28} />
          </div>

          <div>
            <span>Alertas de vencimiento</span>
            <strong>2</strong>
          </div>

          <button type="button">
            Ver detalles
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon danger">
            <Boxes size={28} />
          </div>

          <div>
            <span>Inventario bajo</span>
            <strong>3</strong>
          </div>

          <button type="button">
            Ver detalles
          </button>
        </article>
      </div>

      <section className="dashboard-chart-card">
        <div className="dashboard-chart-header">
          <div>
            <p className="dashboard-eyebrow">
              Resumen semanal
            </p>

            <h2>
              Ventas registradas
            </h2>
          </div>

          <TrendingUp size={24} />
        </div>

        <div className="dashboard-bars">
          <div className="dashboard-bar-item">
            <span>Lun</span>
            <div style={{ height: '42%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Mar</span>
            <div style={{ height: '68%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Mié</span>
            <div style={{ height: '55%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Jue</span>
            <div style={{ height: '82%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Vie</span>
            <div style={{ height: '72%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Sáb</span>
            <div style={{ height: '90%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Dom</span>
            <div style={{ height: '60%' }} />
          </div>
        </div>
      </section>
    </section>
  )
}

export default AdminHome