import { useState } from 'react'
import {
  ChevronDown,
  CreditCard,
  Dumbbell,
  LayoutDashboard,
  LogOut,
  Menu,
  Package,
  Settings,
  ShoppingCart,
  UserRound,
  Users,
  X,
} from 'lucide-react'
import {
  NavLink,
  Outlet,
  useNavigate,
} from 'react-router-dom'
import useAuth from '../../hooks/useAuth'
import './MainLayout.css'

/**
 * Menús disponibles según el rol autenticado.
 *
 * Los módulos que todavía no construimos aparecen
 * deshabilitados temporalmente.
 */
const menusByRole = {
  Administrador: [
    {
      label: 'Inicio',
      path: '/inicio',
      icon: LayoutDashboard,
    },
    {
      label: 'Usuarios',
      icon: Users,
      disabled: true,
    },
    {
      label: 'Membresías',
      icon: CreditCard,
      disabled: true,
    },
    {
      label: 'Inventario',
      icon: Package,
      disabled: true,
    },
    {
      label: 'Ventas',
      icon: ShoppingCart,
      disabled: true,
    },
    {
      label: 'Rutinas',
      icon: Dumbbell,
      disabled: true,
    },
  ],

  Empleado: [
    {
      label: 'Inicio',
      path: '/inicio',
      icon: LayoutDashboard,
    },
    {
      label: 'Clientes',
      icon: Users,
      disabled: true,
    },
    {
      label: 'Membresías',
      icon: CreditCard,
      disabled: true,
    },
    {
      label: 'Inventario',
      icon: Package,
      disabled: true,
    },
    {
      label: 'Ventas',
      icon: ShoppingCart,
      disabled: true,
    },
    {
      label: 'Rutinas',
      icon: Dumbbell,
      disabled: true,
    },
  ],

  Cliente: [
    {
      label: 'Inicio',
      path: '/inicio',
      icon: LayoutDashboard,
    },
    {
      label: 'Mi membresía',
      icon: CreditCard,
      disabled: true,
    },
    {
      label: 'Historial',
      icon: ShoppingCart,
      disabled: true,
    },
    {
      label: 'Rutinas',
      icon: Dumbbell,
      disabled: true,
    },
  ],
}

function MainLayout() {
  const navigate = useNavigate()

  const {
    user,
    logout,
  } = useAuth()

  const [sidebarOpen, setSidebarOpen] =
    useState(false)

  const [userMenuOpen, setUserMenuOpen] =
    useState(false)

  const menuItems =
    menusByRole[user?.role] ??
    menusByRole.Cliente

  /**
   * Genera las iniciales que se muestran cuando
   * el usuario todavía no tiene fotografía.
   */
  const userInitials = user?.name
    ? user.name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((word) => word[0]?.toUpperCase())
        .join('')
    : 'GH'

  const handleLogout = async () => {
    await logout()

    navigate('/login', {
      replace: true,
    })
  }

  return (
    <div className="main-layout">
      <header className="main-header">
        <div className="main-header-left">
          <button
            className="mobile-menu-button"
            type="button"
            aria-label="Abrir menú"
            onClick={() =>
              setSidebarOpen(true)
            }
          >
            <Menu size={24} />
          </button>

          <NavLink
            className="main-brand"
            to="/inicio"
          >
            <div className="main-brand-icon">
              <Dumbbell size={27} />
            </div>

            <div>
              <p className="main-brand-name">
                <span>Gym</span>
                <strong>Hub</strong>
              </p>

              <small>
                Sistema de Gestión y Bienestar
              </small>
            </div>
          </NavLink>
        </div>

        <div className="main-user-container">
          <button
            className="main-user-button"
            type="button"
            aria-expanded={userMenuOpen}
            onClick={() =>
              setUserMenuOpen(
                (previous) => !previous,
              )
            }
          >
            <div className="main-user-avatar">
              {user?.foto_perfil_url ? (
                <img
                  src={user.foto_perfil_url}
                  alt={`Fotografía de ${user.name}`}
                />
              ) : (
                <span>{userInitials}</span>
              )}
            </div>

            <div className="main-user-info">
              <span>Bienvenido</span>
              <strong>{user?.name}</strong>
            </div>

            <ChevronDown
              className={
                userMenuOpen
                  ? 'user-chevron-open'
                  : ''
              }
              size={20}
            />
          </button>

          {userMenuOpen && (
            <div className="main-user-dropdown">
              <button
                type="button"
                disabled
                title="Este módulo se construirá después"
              >
                <Settings size={19} />
                Configuración
              </button>

              <button
                className="logout-option"
                type="button"
                onClick={handleLogout}
              >
                <LogOut size={19} />
                Cerrar sesión
              </button>
            </div>
          )}
        </div>
      </header>

      <div className="main-body">
        {sidebarOpen && (
          <button
            className="sidebar-overlay"
            type="button"
            aria-label="Cerrar menú"
            onClick={() =>
              setSidebarOpen(false)
            }
          />
        )}

        <aside
          className={`main-sidebar ${
            sidebarOpen
              ? 'main-sidebar-open'
              : ''
          }`}
        >
          <div className="sidebar-mobile-header">
            <span>Menú principal</span>

            <button
              type="button"
              aria-label="Cerrar menú"
              onClick={() =>
                setSidebarOpen(false)
              }
            >
              <X size={22} />
            </button>
          </div>

          <nav className="main-navigation">
            {menuItems.map((item) => {
              const Icon = item.icon

              if (item.disabled) {
                return (
                  <button
                    className="navigation-item navigation-item-disabled"
                    type="button"
                    key={item.label}
                    title="Módulo en construcción"
                    disabled
                  >
                    <Icon size={21} />
                    <span>{item.label}</span>
                  </button>
                )
              }

              return (
                <NavLink
                  className={({ isActive }) =>
                    `navigation-item ${
                      isActive
                        ? 'navigation-item-active'
                        : ''
                    }`
                  }
                  key={item.label}
                  to={item.path}
                  onClick={() =>
                    setSidebarOpen(false)
                  }
                >
                  <Icon size={21} />
                  <span>{item.label}</span>
                </NavLink>
              )
            })}
          </nav>

          <div className="sidebar-role">
            <UserRound size={18} />

            <div>
              <span>Rol actual</span>
              <strong>{user?.role}</strong>
            </div>
          </div>
        </aside>

        <section className="main-content">
          <Outlet />
        </section>
      </div>
    </div>
  )
}

export default MainLayout