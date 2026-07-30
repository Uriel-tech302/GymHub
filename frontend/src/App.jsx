import {
  Navigate,
  Route,
  Routes,
} from 'react-router-dom'
import LoginPage from './pages/auth/LoginPage'
import HomePage from './pages/shared/HomePage'
import ProtectedRoute from './routes/ProtectedRoute'
import RegisterPage from './pages/auth/RegisterPage'
import ForgotPasswordPage from './pages/auth/ForgotPasswordPage'
import ResetPasswordPage from './pages/auth/ResetPasswordPage'
import MainLayout from './components/layout/MainLayout'
import UsersPage from './pages/admin/UsersPage'
function App() {
  return (
    <Routes>
      <Route
        path="/login"
        element={<LoginPage />}
      />
      <Route
        path="/registro"
        element={<RegisterPage />}
      />
      <Route
        path="/recuperar-contrasena"
        element={<ForgotPasswordPage />}
      />
      <Route
        path="/reset-password"
        element={<ResetPasswordPage />}
      />
      <Route
        element={
          <ProtectedRoute
            allowedRoles={[
              'Administrador',
              'Empleado',
              'Cliente',
            ]}
          >
            <MainLayout />
          </ProtectedRoute>
        }
      >
        <Route
          path="/inicio"
          element={<HomePage />}
        />
      </Route>
      <Route
        path="/usuarios"
        element={
          <ProtectedRoute
            allowedRoles={['Administrador']}
          >
            <UsersPage />
          </ProtectedRoute>
        }
      />
      <Route
        path="/"
        element={
          <Navigate
            to="/login"
            replace
          />
        }
      />

      <Route
        path="*"
        element={
          <Navigate
            to="/login"
            replace
          />
        }
      />
    </Routes>
  )
}

export default App