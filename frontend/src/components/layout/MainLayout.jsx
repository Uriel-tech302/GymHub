import { useState } from 'react'
import {
  Boxes,
  ChevronDown,
  Dumbbell,
  Home,
  LogOut,
  Settings,
  ShoppingCart,
  Users,
  WalletCards,
} from 'lucide-react'
import { NavLink, Outlet } from 'react-router-dom'
import useAuth from '../../hooks/useAuth'
import './MainLayout.css'

function MainLayout() {
  const { user, logout } = useAuth()
  const [menuUsuarioAbierto, setMenuUsuarioAbierto] =
    useState(false)

  const iniciales = user?.name
    ?.split(' ')
    .slice(0, 2)
    .map((palabra) => palabra[0])
    .join('')
    .toUpperCase()

  const opcionesPorRol = {
    Administrador: [
      {
        to: '/inicio',
        label: 'Inicio',
        icon: Home,
      },
      {
        to: '/usuarios',
        label: 'Usuarios',
        icon: Users,
      },
      {
        to: '/membresias',
        label: 'Membresías',
        icon: WalletCards,
      },
      {
        to: '/inventario',
        label: 'Inventario',
        icon: Boxes,
      },
      {
        to: '/ventas',
        label: 'Ventas',
        icon: ShoppingCart,
      },
      {
        to: '/rutinas',
        label: 'Rutinas',
        icon: Dumbbell,
      },
    ],

    Empleado: [
      {
        to: '/inicio',
        label: 'Inicio',
        icon: Home,
      },
      {
        to: '/membresias',
        label: 'Membresías',
        icon: WalletCards,
      },
      {
        to: '/inventario',
        label: 'Inventario',
        icon: Boxes,
      },
      {
        to: '/ventas',
        label: 'Ventas',
        icon: ShoppingCart,
      },
      {
        to: '/rutinas',
        label: 'Rutinas',
        icon: Dumbbell,
      },
    ],

    Cliente: [
      {
        to: '/inicio',
        label: 'Inicio',
        icon: Home,
      },
      {
        to: '/mi-membresia',
        label: 'Mi membresía',
        icon: WalletCards,
      },
      {
        to: '/historial',
        label: 'Historial',
        icon: ShoppingCart,
      },
      {
        to: '/rutinas',
        label: 'Rutinas',
        icon: Dumbbell,
      },
    ],
  }

  const opciones =
    opcionesPorRol[user?.role] ??
    opcionesPorRol.Cliente

  const cerrarSesion = async () => {
    await logout()
  }

  return (
    <div className="main-layout">
      <header className="main-header">
        <div className="main-brand">
          <div className="main-brand-icon">
            <Dumbbell size={30} />
          </div>

          <div>
            <p className="main-brand-name">
              <span>Gym</span>
              <strong>Hub</strong>
            </p>

            <p className="main-brand-description">
              Sistema de Gestión y Bienestar
            </p>
          </div>
        </div>

        <div className="main-user">
          <button
            type="button"
            className="main-user-button"
            onClick={() =>
              setMenuUsuarioAbierto(
                (estadoAnterior) =>
                  !estadoAnterior,
              )
            }
          >
            <span className="main-user-avatar">
              {iniciales || 'GH'}
            </span>

            <span className="main-user-info">
              <small>Bienvenido</small>
              <strong>{user?.name}</strong>
            </span>

            <ChevronDown
              size={18}
              className={
                menuUsuarioAbierto
                  ? 'main-user-chevron open'
                  : 'main-user-chevron'
              }
            />
          </button>

          {menuUsuarioAbierto && (
            <div className="main-user-menu">
              <button type="button">
                <Settings size={18} />
                Configuración
              </button>

              <button
                type="button"
                className="logout-option"
                onClick={cerrarSesion}
              >
                <LogOut size={18} />
                Cerrar sesión
              </button>
            </div>
          )}
        </div>
      </header>

      <nav className="main-navigation">
        {opciones.map(
          ({
            to,
            label,
            icon: Icon,
          }) => (
            <NavLink
              key={to}
              to={to}
              className={({ isActive }) =>
                isActive
                  ? 'main-navigation-link active'
                  : 'main-navigation-link'
              }
            >
              <Icon size={19} />
              <span>{label}</span>
            </NavLink>
          ),
        )}
      </nav>

      <main className="main-content">
        <Outlet />
      </main>

      <footer className="main-footer">
        © 2026 GymHub. Todos los derechos
        reservados.
      </footer>
    </div>
  )
}

export default MainLayout