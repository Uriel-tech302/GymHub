import { useEffect, useState } from 'react'
import {
    ArrowLeft,
    Filter,
    RefreshCw,
    Search,
    Users,
} from 'lucide-react'
import api from '../../api/axios'
import './UsersPage.css'
import { useNavigate } from 'react-router-dom'
const metaInicial = {
    current_page: 1,
    last_page: 1,
    total: 0,
    from: 0,
    to: 0,
}

function UsersPage() {
    const navigate = useNavigate()
    const [usuarios, setUsuarios] = useState([])
    const [busqueda, setBusqueda] = useState('')
    const [rol, setRol] = useState('')

    const [busquedaAplicada, setBusquedaAplicada] =
        useState('')
    const [rolAplicado, setRolAplicado] =
        useState('')

    const [pagina, setPagina] = useState(1)
    const [meta, setMeta] = useState(metaInicial)

    const [cargando, setCargando] = useState(true)
    const [error, setError] = useState('')

    /**
     * Consulta los usuarios desde Laravel.
     */
    useEffect(() => {
        let componenteActivo = true

        const obtenerUsuarios = async () => {
            setCargando(true)
            setError('')

            try {
                const response = await api.get('/users', {
                    params: {
                        search: busquedaAplicada || undefined,
                        role: rolAplicado || undefined,
                        page: pagina,
                        per_page: 10,
                    },
                })

                if (!componenteActivo) {
                    return
                }

                setUsuarios(response.data.data ?? [])
                setMeta(response.data.meta ?? metaInicial)
            } catch (exception) {
                if (!componenteActivo) {
                    return
                }

                const mensaje =
                    exception.response?.data?.message ??
                    'No fue posible obtener los usuarios.'

                setUsuarios([])
                setError(mensaje)
            } finally {
                if (componenteActivo) {
                    setCargando(false)
                }
            }
        }

        obtenerUsuarios()

        return () => {
            componenteActivo = false
        }
    }, [
        pagina,
        busquedaAplicada,
        rolAplicado,
    ])

    /**
     * Aplica la búsqueda y el filtro seleccionado.
     */
    const manejarFiltros = (event) => {
        event.preventDefault()

        setPagina(1)
        setBusquedaAplicada(busqueda.trim())
        setRolAplicado(rol)
    }

    /**
     * Limpia todos los filtros.
     */
    const limpiarFiltros = () => {
        setBusqueda('')
        setRol('')
        setBusquedaAplicada('')
        setRolAplicado('')
        setPagina(1)
    }

    const formatearFecha = (fecha) => {
        if (!fecha) {
            return 'Sin fecha'
        }

        return new Intl.DateTimeFormat('es-MX', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(new Date(fecha))
    }

    return (
        <section className="users-page">
            <header className="users-header">
                <div className="users-header-content">
                    <button
                        className="users-back-button"
                        type="button"
                        onClick={() => navigate('/inicio')}
                    >
                        <ArrowLeft size={18} />
                        Volver al inicio
                    </button>

                    <p className="users-eyebrow">
                        Administración
                    </p>

                    <h1>Usuarios</h1>

                    <p>
                        Consulta las cuentas registradas y filtra
                        la información por nombre, correo o rol.
                    </p>
                </div>

                <div className="users-total-card">
                    <div className="users-total-icon">
                        <Users size={25} />
                    </div>

                    <div>
                        <span>Total registrado</span>
                        <strong>{meta.total ?? 0}</strong>
                    </div>
                </div>
            </header>

            <form
                className="users-filters"
                onSubmit={manejarFiltros}
            >
                <div className="users-search">
                    <Search size={19} />

                    <input
                        type="search"
                        placeholder="Buscar por nombre o correo"
                        value={busqueda}
                        onChange={(event) =>
                            setBusqueda(event.target.value)
                        }
                    />
                </div>

                <div className="users-role-filter">
                    <Filter size={18} />

                    <select
                        value={rol}
                        onChange={(event) =>
                            setRol(event.target.value)
                        }
                    >
                        <option value="">
                            Todos los roles
                        </option>

                        <option value="Administrador">
                            Administrador
                        </option>

                        <option value="Empleado">
                            Empleado
                        </option>

                        <option value="Cliente">
                            Cliente
                        </option>
                    </select>
                </div>

                <button
                    className="users-search-button"
                    type="submit"
                >
                    Buscar
                </button>

                <button
                    className="users-clear-button"
                    type="button"
                    onClick={limpiarFiltros}
                >
                    <RefreshCw size={17} />
                    Limpiar
                </button>
            </form>

            {error && (
                <div
                    className="users-error"
                    role="alert"
                >
                    {error}
                </div>
            )}

            <div className="users-table-card">
                {cargando ? (
                    <div className="users-state">
                        <span className="users-loader" />

                        <p>Cargando usuarios...</p>
                    </div>
                ) : usuarios.length === 0 ? (
                    <div className="users-state">
                        <Users size={38} />

                        <h2>No se encontraron usuarios</h2>

                        <p>
                            Intenta cambiar los filtros de búsqueda.
                        </p>
                    </div>
                ) : (
                    <div className="users-table-container">
                        <table className="users-table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Registro</th>
                                </tr>
                            </thead>

                            <tbody>
                                {usuarios.map((usuario) => (
                                    <tr key={usuario.id}>
                                        <td>
                                            <div className="users-person">
                                                <div className="users-avatar">
                                                    {usuario.name
                                                        ?.split(' ')
                                                        .slice(0, 2)
                                                        .map(
                                                            (palabra) =>
                                                                palabra[0],
                                                        )
                                                        .join('')
                                                        .toUpperCase() || 'US'}
                                                </div>

                                                <div>
                                                    <strong>
                                                        {usuario.name}
                                                    </strong>

                                                    <span>
                                                        {usuario.email}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            {usuario.telefono ||
                                                'Sin teléfono'}
                                        </td>

                                        <td>
                                            <span
                                                className={`users-role users-role-${usuario.role?.toLowerCase()}`}
                                            >
                                                {usuario.role}
                                            </span>
                                        </td>

                                        <td>
                                            {formatearFecha(
                                                usuario.created_at,
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                {!cargando && usuarios.length > 0 && (
                    <footer className="users-pagination">
                        <p>
                            Mostrando {meta.from ?? 0}–
                            {meta.to ?? 0} de {meta.total ?? 0}
                        </p>

                        <div>
                            <button
                                type="button"
                                disabled={
                                    meta.current_page <= 1
                                }
                                onClick={() =>
                                    setPagina(
                                        (paginaAnterior) =>
                                            paginaAnterior - 1,
                                    )
                                }
                            >
                                Anterior
                            </button>

                            <span>
                                Página {meta.current_page} de{' '}
                                {meta.last_page}
                            </span>

                            <button
                                type="button"
                                disabled={
                                    meta.current_page >=
                                    meta.last_page
                                }
                                onClick={() =>
                                    setPagina(
                                        (paginaAnterior) =>
                                            paginaAnterior + 1,
                                    )
                                }
                            >
                                Siguiente
                            </button>
                        </div>
                    </footer>
                )}
            </div>
        </section>
    )
}

export default UsersPage