import { useState } from 'react'
import {
  Navigate,
  useNavigate,
} from 'react-router-dom'
import useAuth from '../../hooks/useAuth'

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
  const [submitting, setSubmitting] = useState(false)

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

    if (
      Object.keys(validationErrors).length > 0
    ) {
      setErrors(validationErrors)
      return
    }

    setSubmitting(true)
    setGeneralError('')

    try {
      await login({
        email: form.email
          .trim()
          .toLowerCase(),

        password: form.password,
      })

      navigate('/inicio', {
        replace: true,
      })
    } catch (error) {
      const status = error.response?.status
      const data = error.response?.data

      if (
        status === 422 &&
        data?.errors
      ) {
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
            'No fue posible conectar con GymHub.',
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
    <main>
      <section>
        <h1>Iniciar sesión en GymHub</h1>

        {generalError && (
          <p role="alert">
            {generalError}
          </p>
        )}

        <form
          onSubmit={handleSubmit}
          noValidate
        >
          <div>
            <label htmlFor="email">
              Correo electrónico
            </label>

            <input
              id="email"
              name="email"
              type="email"
              value={form.email}
              onChange={handleChange}
              autoComplete="email"
            />

            {errors.email && (
              <p>{errors.email}</p>
            )}
          </div>

          <div>
            <label htmlFor="password">
              Contraseña
            </label>

            <input
              id="password"
              name="password"
              type="password"
              value={form.password}
              onChange={handleChange}
              autoComplete="current-password"
            />

            {errors.password && (
              <p>{errors.password}</p>
            )}
          </div>

          <button
            type="submit"
            disabled={submitting}
          >
            {submitting
              ? 'Iniciando sesión...'
              : 'Iniciar sesión'}
          </button>
        </form>
      </section>
    </main>
  )
}

export default LoginPage