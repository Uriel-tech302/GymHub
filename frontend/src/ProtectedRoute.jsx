import React from 'react';

export const ProtectedRoute = ({ isAllowed, children, redirectTo = "/" }) => {
  // Si el usuario no cumple con el rol o no está autenticado, no se le da acceso
  if (!isAllowed) {
    return (
      <div style={{ padding: '40px', textAlign: 'center' }}>
        <h2>⛔ Acceso Denegado</h2>
        <p>No tienes los permisos necesarios (rol) para ver esta sección.</p>
      </div>
    );
  }

  return children;
};