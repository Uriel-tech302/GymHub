import { useState } from 'react'
import {
  ArrowLeft,
  CheckCircle2,
  Eye,
  EyeOff,
  LockKeyhole,
  Mail,
} from 'lucide-react'
import {
  Link,
  useSearchParams,
} from 'react-router-dom'
import api from '../../api/axios'
import './LoginPage.css'

const initialForm = {
  password: '',
  password_confirmation: '',
}

function ResetPasswordPage() {
  const [searchParams] = useSearchParams()

  /**
   * Estos datos vienen dentro del enlace generado
   * por Laravel y guardado en laravel.log.
   */
  const token = searchParams.get('token') ?? ''
  const email = searchParams.get('email') ?? ''

  const [form, setForm] = useState(initialForm)
  const [errors, setErrors] = useState({})
  const [generalError, setGeneralError] = useState('')
  const [successMessage, setSuccessMessage] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmation, setShowConfirmation] =
    useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [completed, setCompleted] = useState(false)

  const passwordRules = {
    length: form.password.length >= 8,
    uppercase: /[A-Z]/.test(form.password),
    lowercase: /[a-z]/.test(form.password),
    number: /\d/.test(form.password),
    special: /[^A-Za-z0-9]/.test(form.password),
  }

  const invalidLink = !token || !email

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

    const validPassword =
      passwordRules.length &&
      passwordRules.uppercase &&
      passwordRules.lowercase &&
      passwordRules.number &&
      passwordRules.special

    if (!form.password) {
      newErrors.password =
        'La nueva contraseña es obligatoria.'
    } else if (!validPassword) {
      newErrors.password =
        'La contraseña no cumple todos los requisitos.'
    }

    if (!form.password_confirmation) {
      newErrors.password_confirmation =
        'Confirma tu nueva contraseña.'
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
    setSuccessMessage('')

    try {
      const response = await api.post(
        '/reset-password',
        {
          token,
          email,
          password: form.password,
          password_confirmation:
            form.password_confirmation,
        },
      )

      setSuccessMessage(response.data.message)
      setCompleted(true)
      setForm(initialForm)
    } catch (requestError) {
      const status = requestError.response?.status
      const data = requestError.response?.data

      if (status === 422 && data?.errors) {
        const backendErrors = {}

        Object.entries(data.errors).forEach(
          ([field, messages]) => {
            backendErrors[field] = messages[0]
          },
        )

        setErrors(backendErrors)

        if (backendErrors.token) {
          setGeneralError(
            data?.message ??
              backendErrors.token,
          )
        }
      } else {
        setGeneralError(
          data?.message ??
            'No fue posible restablecer la contraseña.',
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
          Restablecer contraseña
        </h1>

        {invalidLink ? (
          <>
            <div
              className="login-general-error"
              role="alert"
            >
              El enlace está incompleto o no contiene
              los datos necesarios.
            </div>

            <Link
              className="forgot-back-link"
              to="/recuperar-contrasena"
            >
              <ArrowLeft size={17} />
              Solicitar un enlace nuevo
            </Link>
          </>
        ) : completed ? (
          <div className="reset-completed">
            <CheckCircle2 size={46} />

            <h2>Contraseña actualizada</h2>

            <p>{successMessage}</p>

            <Link
              className="login-submit reset-login-button"
              to="/login"
            >
              Iniciar sesión
            </Link>
          </div>
        ) : (
          <>
            <p className="forgot-description">
              Crea una contraseña nueva para la cuenta:
            </p>

            <div className="reset-email-container">
              <Mail size={18} />
              <span>{email}</span>
            </div>

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
                <label htmlFor="password">
                  Nueva contraseña
                </label>

                <div
                  className={`login-input-container ${
                    errors.password
                      ? 'has-error'
                      : ''
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
                    placeholder="Ingresa la contraseña nueva"
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
                    placeholder="Repite la contraseña nueva"
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
                  ? 'Actualizando contraseña...'
                  : 'Guardar nueva contraseña'}
              </button>
            </form>

            <Link
              className="forgot-back-link"
              to="/login"
            >
              <ArrowLeft size={17} />
              Regresar al inicio de sesión
            </Link>
          </>
        )}
      </section>
    </main>
  )
}

export default ResetPasswordPage