import React, { useState } from 'react';
import { Navbar } from './Navbar';
import { UsersTable } from './UsersTable';
import { ProtectedRoute } from './ProtectedRoute';
import Login from './Login';
import './App.css';

function App() {
  const [isAuthenticated, setIsAuthenticated] = useState(true);

  // Datos del usuario con Rol para las rutas protegidas
  const [user, setUser] = useState({
    name: 'Uriel Admin',
    email: 'admin@gymhub.com',
    role: 'Admin', // Cambia a 'Cliente' para probar que bloquea el acceso
    avatar: 'https://via.placeholder.com/40'
  });

  const handleLogout = () => {
    localStorage.removeItem('token');
    setIsAuthenticated(false);
  };

  if (!isAuthenticated) {
    return <Login onLoginSuccess={() => setIsAuthenticated(true)} />;
  }

  return (
    <div className="main-app">
      <Navbar user={user} onLogout={handleLogout} />

      <main style={{ padding: '20px' }}>
        {/* RUTA PROTEGIDA: Solo accesible si el rol es 'Admin' */}
        <ProtectedRoute isAllowed={user && user.role === 'Admin'}>
          <UsersTable />
        </ProtectedRoute>
      </main>
    </div>
  );
}

export default App;