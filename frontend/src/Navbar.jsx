import React from 'react';

export const Navbar = ({ user, onLogout }) => {
  // Datos por defecto por si aún no viene la respuesta del backend
  const currentUser = user || {
    name: 'Usuario GymHub',
    email: 'usuario@gymhub.com',
    role: 'Cliente',
    avatar: 'https://via.placeholder.com/40'
  };

  return (
    <nav className="navbar">
      <div className="navbar-brand">
        <h2>GymHub</h2>
      </div>

      <div className="navbar-user">
        <img 
          src={currentUser.avatar} 
          alt="Avatar de usuario" 
          className="user-avatar" 
        />
        <div className="user-info">
          <span className="user-name">{currentUser.name}</span>
          <span className="user-email">{currentUser.email}</span>
        </div>

        <button onClick={onLogout} className="btn-logout">
          Cerrar Sesión
        </button>
      </div>
    </nav>
  );
};