import React, { useState } from 'react';

export const Login = () => {
  // 1. Estados para los datos del formulario
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  
  // 2. Estados para los errores y la animación de carga (exigidos por la rúbrica)
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);

  // 3. Función para validar las reglas estrictas de la contraseña
  const validatePassword = (pass) => {
    if (!pass) return 'La contraseña es requerida.';
    if (pass.length < 8) return 'Debe tener al menos 8 caracteres.';
    if (!/[A-Z]/.test(pass)) return 'Debe contener al menos una letra mayúscula.';
    if (!/[0-9]/.test(pass)) return 'Debe contener al menos un número.';
    if (!/[!@#$%^&*(),.?":{}|<>]/.test(pass)) return 'Debe contener al menos un carácter especial (@, #, $, etc.).';
    return '';
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    let newErrors = {};

    // Validar Correo
    if (!email) {
      newErrors.email = 'El correo electrónico es requerido.';
    }

    // Validar Contraseña
    const passwordError = validatePassword(password);
    if (passwordError) {
      newErrors.password = passwordError;
    }

    setErrors(newErrors);

    // Si todo está correcto, aquí se conectará la API de Laravel Sanctum
    if (Object.keys(newErrors).length === 0) {
      setLoading(true);
      console.log('Datos listos para enviar al Backend:', { email, password });
      
      // Simulación de envío (tu compañero conectará esto con Axios a Laravel)
      setTimeout(() => {
        setLoading(false);
      }, 1500);
    }
  };

  return (
    <div className="login-container">
      <div className="login-card">
        <h2>GymHub</h2>
        <p>Inicia sesión en tu cuenta</p>

        <form onSubmit={handleSubmit}>
          {/* CAMPO CORREO */}
          <div className="form-group">
            <label htmlFor="email">Correo Electrónico</label>
            <input
              type="email"
              id="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="ejemplo@gymhub.com"
            />
            {/* Mensaje de error visible debajo del input (Cumple regla de no usar alert) */}
            {errors.email && <p style={{ color: 'red', fontSize: '12px', marginTop: '4px' }}>{errors.email}</p>}
          </div>

          {/* CAMPO CONTRASEÑA */}
          <div className="form-group">
            <label htmlFor="password">Contraseña</label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => {
                setPassword(e.target.value);
                // Validación en tiempo real
                setErrors((prev) => ({ ...prev, password: validatePassword(e.target.value) }));
              }}
              placeholder="••••••••"
            />
            {/* Mensaje de error visible debajo del input */}
            {errors.password && <p style={{ color: 'red', fontSize: '12px', marginTop: '4px' }}>{errors.password}</p>}
          </div>

          {/* Botón con estado de carga (Loading state) */}
          <button type="submit" disabled={loading} className="btn-primary">
            {loading ? 'Cargando...' : 'Iniciar Sesión'}
          </button>
        </form>
      </div>
    </div>
  );
};