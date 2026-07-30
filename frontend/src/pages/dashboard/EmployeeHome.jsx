import {
  AlertTriangle,
  Boxes,
  ClipboardList,
  DollarSign,
  TrendingUp,
} from 'lucide-react'
import './DashboardHome.css'

function EmployeeHome({ user }) {
  return (
    <section className="dashboard-home">
      <header className="dashboard-welcome">
        <div>
          <p className="dashboard-eyebrow">
            Panel operativo
          </p>

          <h1>
            Hola, {user.name}
          </h1>

          <p>
            Consulta las actividades del día y accede
            rápidamente a las funciones operativas de GymHub.
          </p>
        </div>

        <span className="dashboard-status">
          Empleado
        </span>
      </header>

      <div className="dashboard-grid">
        <article className="dashboard-card">
          <div className="dashboard-card-icon">
            <DollarSign size={28} />
          </div>

          <div>
            <span>Ventas del día</span>
            <strong>$620.00</strong>
          </div>

          <button type="button">
            Ver ventas
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon warning">
            <AlertTriangle size={28} />
          </div>

          <div>
            <span>Membresías por vencer</span>
            <strong>2</strong>
          </div>

          <button type="button">
            Ver membresías
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon danger">
            <Boxes size={28} />
          </div>

          <div>
            <span>Productos con poco stock</span>
            <strong>3</strong>
          </div>

          <button type="button">
            Ver inventario
          </button>
        </article>

        <article className="dashboard-card">
          <div className="dashboard-card-icon success">
            <ClipboardList size={28} />
          </div>

          <div>
            <span>Rutinas disponibles</span>
            <strong>8</strong>
          </div>

          <button type="button">
            Ver rutinas
          </button>
        </article>
      </div>

      <section className="dashboard-chart-card">
        <div className="dashboard-chart-header">
          <div>
            <p className="dashboard-eyebrow">
              Actividad semanal
            </p>

            <h2>
              Operaciones registradas
            </h2>
          </div>

          <TrendingUp size={24} />
        </div>

        <div className="dashboard-bars">
          <div className="dashboard-bar-item">
            <span>Lun</span>
            <div style={{ height: '48%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Mar</span>
            <div style={{ height: '70%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Mié</span>
            <div style={{ height: '58%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Jue</span>
            <div style={{ height: '76%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Vie</span>
            <div style={{ height: '88%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Sáb</span>
            <div style={{ height: '66%' }} />
          </div>

          <div className="dashboard-bar-item">
            <span>Dom</span>
            <div style={{ height: '35%' }} />
          </div>
        </div>
      </section>
    </section>
  )
}

export default EmployeeHome