import { useState } from 'react'
import {
  Eye,
  EyeOff,
  LockKeyhole,
  Mail,
  UserRound,
} from 'lucide-react'
import {
  Link,
  Navigate,
  useNavigate,
} from 'react-router-dom'
import useAuth from '../../hooks/useAuth'
import './LoginPage.css'

const initialForm = {
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
}

function RegisterPage() {
  const navigate = useNavigate()

  const {
    register,
    isAuthenticated,
  } = useAuth()

  const [form, setForm] = useState(initialForm)
  const [errors, setErrors] = useState({})
  const [generalError, setGeneralError] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmation, setShowConfirmation] =
    useState(false)
  const [submitting, setSubmitting] = useState(false)

  const passwordRules = {
    length: form.password.length >= 8,
    uppercase: /[A-Z]/.test(form.password),
    lowercase: /[a-z]/.test(form.password),
    number: /\d/.test(form.password),
    special: /[^A-Za-z0-9]/.test(form.password),
  }

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

  const validateForm = () => {
    const newErrors = {}

    if (!form.name.trim()) {
      newErrors.name =
        'El nombre completo es obligatorio.'
    }

    if (!form.email.trim()) {
      newErrors.email =
        'El correo electrónico es obligatorio.'
    } else if (
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)
    ) {
      newErrors.email =
        'Ingresa un correo electrónico válido.'
    }

    const validPassword =
      passwordRules.length &&
      passwordRules.uppercase &&
      passwordRules.lowercase &&
      passwordRules.number &&
      passwordRules.special

    if (!form.password) {
      newErrors.password =
        'La contraseña es obligatoria.'
    } else if (!validPassword) {
      newErrors.password =
        'La contraseña no cumple todos los requisitos.'
    }

    if (!form.password_confirmation) {
      newErrors.password_confirmation =
        'Confirma tu contraseña.'
    } else if (
      form.password !== form.password_confirmation
    ) {
      newErrors.password_confirmation =
        'Las contraseñas no coinciden.'
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
      await register({
        name: form.name.trim(),
        email: form.email.trim().toLowerCase(),
        password: form.password,
        password_confirmation:
          form.password_confirmation,
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
            'No fue posible crear la cuenta. Verifica la conexión con Laravel.',
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
      <section className="login-card register-card">
        <header className="login-brand">
          
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
          Crear una cuenta
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
            <label htmlFor="name">
              Nombre completo
            </label>

            <div
              className={`login-input-container ${
                errors.name ? 'has-error' : ''
              }`}
            >
              <UserRound size={20} />

              <input
                id="name"
                name="name"
                type="text"
                placeholder="Nombre y apellidos"
                value={form.name}
                onChange={handleChange}
                autoComplete="name"
              />
            </div>

            {errors.name && (
              <p className="login-field-error">
                {errors.name}
              </p>
            )}
          </div>

          <div className="login-form-group">
            <label htmlFor="email">
              Correo electrónico
            </label>

            <div
              className={`login-input-container ${
                errors.email ? 'has-error' : ''
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
              className={`login-input-container ${
                errors.password ? 'has-error' : ''
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
                placeholder="Crea una contraseña"
                value={form.password}
                onChange={handleChange}
                autoComplete="new-password"
              />

              <button
                className="login-password-button"
                type="button"
                aria-label="Mostrar u ocultar contraseña"
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

          <div className="login-requirements">
            <strong>
              Requisitos de contraseña
            </strong>

            <span>
              {passwordRules.length ? '✓' : '•'}
              {' '}Al menos 8 caracteres
            </span>

            <span>
              {passwordRules.uppercase ? '✓' : '•'}
              {' '}Una letra mayúscula
            </span>

            <span>
              {passwordRules.lowercase ? '✓' : '•'}
              {' '}Una letra minúscula
            </span>

            <span>
              {passwordRules.number ? '✓' : '•'}
              {' '}Un número
            </span>

            <span>
              {passwordRules.special ? '✓' : '•'}
              {' '}Un carácter especial
            </span>
          </div>

          <div className="login-form-group">
            <label htmlFor="password_confirmation">
              Confirmar contraseña
            </label>

            <div
              className={`login-input-container ${
                errors.password_confirmation
                  ? 'has-error'
                  : ''
              }`}
            >
              <LockKeyhole size={20} />

              <input
                id="password_confirmation"
                name="password_confirmation"
                type={
                  showConfirmation
                    ? 'text'
                    : 'password'
                }
                placeholder="Repite tu contraseña"
                value={form.password_confirmation}
                onChange={handleChange}
                autoComplete="new-password"
              />

              <button
                className="login-password-button"
                type="button"
                aria-label="Mostrar u ocultar confirmación"
                onClick={() =>
                  setShowConfirmation(
                    (previous) => !previous,
                  )
                }
              >
                {showConfirmation ? (
                  <EyeOff size={20} />
                ) : (
                  <Eye size={20} />
                )}
              </button>
            </div>

            {errors.password_confirmation && (
              <p className="login-field-error">
                {errors.password_confirmation}
              </p>
            )}
          </div>

          <button
            className="login-submit"
            type="submit"
            disabled={submitting}
          >
            {submitting
              ? 'Creando cuenta...'
              : 'Registrarse'}
          </button>

          <p className="register-login-link">
            ¿Ya tienes cuenta?{' '}
            <Link to="/login">
              Inicia sesión aquí
            </Link>
          </p>
        </form>
      </section>
    </main>
  )
}

export default RegisterPage