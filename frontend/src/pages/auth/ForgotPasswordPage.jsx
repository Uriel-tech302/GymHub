import { useState } from 'react'
import {
  ArrowLeft,
  Mail,
} from 'lucide-react'
import { Link } from 'react-router-dom'
import api from '../../api/axios'
import './LoginPage.css'

function ForgotPasswordPage() {
  const [email, setEmail] = useState('')
  const [error, setError] = useState('')
  const [generalError, setGeneralError] = useState('')
  const [successMessage, setSuccessMessage] = useState('')
  const [submitting, setSubmitting] = useState(false)

  /**
   * Valida el correo antes de enviarlo a Laravel.
   */
  const validateEmail = () => {
    if (!email.trim()) {
      return 'El correo electrónico es obligatorio.'
    }

    if (
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
    ) {
      return 'Ingresa un correo electrónico válido.'
    }

    return ''
  }

  const handleSubmit = async (event) => {
    event.preventDefault()

    const validationError = validateEmail()

    if (validationError) {
      setError(validationError)
      return
    }

    setSubmitting(true)
    setError('')
    setGeneralError('')
    setSuccessMessage('')

    try {
      const response = await api.post(
        '/forgot-password',
        {
          email: email.trim().toLowerCase(),
        },
      )

      setSuccessMessage(response.data.message)
    } catch (requestError) {
      const status = requestError.response?.status
      const data = requestError.response?.data

      if (
        status === 422 &&
        data?.errors?.email
      ) {
        setError(data.errors.email[0])
      } else {
        setGeneralError(
          data?.message ??
            'No fue posible solicitar la recuperación.',
        )
      }
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <main className="login-page">
      <section className="login-card">
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
          Recuperar contraseña
        </h1>

        <p className="forgot-description">
          Ingresa el correo asociado a tu cuenta.
          Te enviaremos un enlace para crear una
          contraseña nueva.
        </p>

        {generalError && (
          <div
            className="login-general-error"
            role="alert"
          >
            {generalError}
          </div>
        )}

        {successMessage && (
          <div
            className="forgot-success"
            role="status"
          >
            {successMessage}
          </div>
        )}

        <form
          onSubmit={handleSubmit}
          noValidate
        >
          <div className="login-form-group">
            <label htmlFor="recovery-email">
              Correo electrónico
            </label>

            <div
              className={`login-input-container ${
                error ? 'has-error' : ''
              }`}
            >
              <Mail size={20} />

              <input
                id="recovery-email"
                name="email"
                type="email"
                placeholder="correo@ejemplo.com"
                value={email}
                onChange={(event) => {
                  setEmail(event.target.value)
                  setError('')
                  setGeneralError('')
                  setSuccessMessage('')
                }}
                autoComplete="email"
              />
            </div>

            {error && (
              <p className="login-field-error">
                {error}
              </p>
            )}
          </div>

          <button
            className="login-submit"
            type="submit"
            disabled={submitting}
          >
            {submitting
              ? 'Enviando enlace...'
              : 'Enviar enlace de recuperación'}
          </button>
        </form>

        <Link
          className="forgot-back-link"
          to="/login"
        >
          <ArrowLeft size={17} />
          Regresar al inicio de sesión
        </Link>
      </section>
    </main>
  )
}

export default ForgotPasswordPage