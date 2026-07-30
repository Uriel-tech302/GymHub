import { useState } from 'react'
import {
  Dumbbell,
  Eye,
  EyeOff,
  LockKeyhole,
  Mail,
} from 'lucide-react'
import {
  Link,
  Navigate,
  useNavigate,
} from 'react-router-dom'
import useAuth from '../../hooks/useAuth'
import './LoginPage.css'

function LoginPage() {
  const navigate = useNavigate()

  const {
    login,
    isAuthenticated,
  } = useAuth()

  const [form, setForm] = useState({
    email: '',
    password: '',
  })

  const [errors, setErrors] = useState({})
  const [generalError, setGeneralError] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [submitting, setSubmitting] = useState(false)

  /**
   * Actualiza los valores del formulario y elimina
   * el error del campo que el usuario está corrigiendo.
   */
  const handleChange = (event) => {
    const {
      name,
      value,
    } = event.target

    setForm((previous) => ({
      ...previous,
      [name]: value,
    }))

    setErrors((previous) => ({
      ...previous,
      [name]: '',
    }))

    setGeneralError('')
  }

  /**
   * Validaciones visibles realizadas en React.
   */
  const validateForm = () => {
    const newErrors = {}

    if (!form.email.trim()) {
      newErrors.email =
        'El correo electrónico es obligatorio.'
    } else if (
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)
    ) {
      newErrors.email =
        'Ingresa un correo electrónico válido.'
    }

    if (!form.password) {
      newErrors.password =
        'La contraseña es obligatoria.'
    }

    return newErrors
  }

  const handleSubmit = async (event) => {
    event.preventDefault()

    const validationErrors = validateForm()

    if (Object.keys(validationErrors).length > 0) {
      setErrors(validationErrors)
      return
    }

    setSubmitting(true)
    setGeneralError('')

    try {
      await login({
        email: form.email.trim().toLowerCase(),
        password: form.password,
      })

      navigate('/inicio', {
        replace: true,
      })
    } catch (error) {
      const status = error.response?.status
      const data = error.response?.data

      if (status === 422 && data?.errors) {
        const backendErrors = {}

        Object.entries(data.errors).forEach(
          ([field, messages]) => {
            backendErrors[field] = messages[0]
          },
        )

        setErrors(backendErrors)
      } else {
        setGeneralError(
          data?.message ??
          'No fue posible conectar con GymHub. Verifica que Laravel esté funcionando.',
        )
      }
    } finally {
      setSubmitting(false)
    }
  }

  if (isAuthenticated) {
    return (
      <Navigate
        to="/inicio"
        replace
      />
    )
  }

  return (
    <main className="login-page">
      <section className="login-card">
        <header className="login-brand">
          <div className="login-logo">
            <Dumbbell size={35} />
          </div>

          <div>
            <p className="login-brand-name">
              <span>Gym</span>
              <strong>Hub</strong>
            </p>

            <p className="login-brand-description">
              Sistema de Gestión y Bienestar
            </p>
          </div>
        </header>

        <div className="login-divider" />

        <h1 className="login-title">
          Iniciar sesión en GymHub
        </h1>

        {generalError && (
          <div
            className="login-general-error"
            role="alert"
          >
            {generalError}
          </div>
        )}

        <form
          onSubmit={handleSubmit}
          noValidate
        >
          <div className="login-form-group">
            <label htmlFor="email">
              Correo electrónico
            </label>

            <div
              className={`login-input-container ${errors.email ? 'has-error' : ''
                }`}
            >
              <Mail size={20} />

              <input
                id="email"
                name="email"
                type="email"
                placeholder="correo@ejemplo.com"
                value={form.email}
                onChange={handleChange}
                autoComplete="email"
              />
            </div>

            {errors.email && (
              <p className="login-field-error">
                {errors.email}
              </p>
            )}
          </div>

          <div className="login-form-group">
            <label htmlFor="password">
              Contraseña
            </label>

            <div
              className={`login-input-container ${errors.password ? 'has-error' : ''
                }`}
            >
              <LockKeyhole size={20} />

              <input
                id="password"
                name="password"
                type={
                  showPassword
                    ? 'text'
                    : 'password'
                }
                placeholder="Ingresa tu contraseña"
                value={form.password}
                onChange={handleChange}
                autoComplete="current-password"
              />

              <button
                className="login-password-button"
                type="button"
                aria-label={
                  showPassword
                    ? 'Ocultar contraseña'
                    : 'Mostrar contraseña'
                }
                onClick={() =>
                  setShowPassword(
                    (previous) => !previous,
                  )
                }
              >
                {showPassword ? (
                  <EyeOff size={20} />
                ) : (
                  <Eye size={20} />
                )}
              </button>
            </div>

            {errors.password && (
              <p className="login-field-error">
                {errors.password}
              </p>
            )}
          </div>

          <p className="login-forgot">
            <Link to="/recuperar-contrasena">
              ¿Olvidaste tu contraseña?
            </Link>
          </p>
          {/**   
          <div className="login-requirements">
            <strong>
              Requisitos de contraseña
            </strong>

            <span>• Al menos 8 caracteres</span>
            <span>• Una letra mayúscula</span>
            <span>• Un número</span>
            <span>• Un carácter especial</span>
          </div>
           */}
          <button
            className="login-submit"
            type="submit"
            disabled={submitting}
          >
            {submitting
              ? 'Iniciando sesión...'
              : 'Iniciar sesión'}
          </button>

          <button
            className="login-register"
            type="button"
            onClick={() => navigate('/registro')}
          >
            Registrarse
          </button>
        </form>
      </section>
    </main>
  )
}

export default LoginPage