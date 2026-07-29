import {
  Navigate,
  Route,
  Routes,
} from 'react-router-dom'
import LoginPage from './pages/auth/LoginPage'
import HomePage from './pages/shared/HomePage'
import ProtectedRoute from './routes/ProtectedRoute'

function App() {
  return (
    <Routes>
      <Route
        path="/login"
        element={<LoginPage />}
      />

      <Route
        path="/inicio"
        element={
          <ProtectedRoute
            allowedRoles={[
              'Administrador',
              'Empleado',
              'Cliente',
            ]}
          >
            <HomePage />
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