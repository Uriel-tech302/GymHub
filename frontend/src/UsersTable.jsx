import React, { useState, useEffect } from 'react';
import api from './api';
import { ConfirmModal } from './ConfirmModal';
import './UsersTable.css';

export const UsersTable = () => {
  // Estados para datos, paginación y búsqueda
  const [users, setUsers] = useState([]);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(false);

  // Estado para el modal de eliminación
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const [deleting, setDeleting] = useState(false);

  // Función para obtener usuarios de la API con filtros y paginación
  const fetchUsers = async () => {
    setLoading(true);
    try {
      // Enviar parámetros de búsqueda y página a la API
      const response = await api.get('/users', {
        params: { search, page }
      });
      
      // Adaptar respuesta según formato típico de Laravel / API
      const data = response.data;
      setUsers(data.data || data); 
      setTotalPages(data.last_page || 1);
    } catch (error) {
      console.error('Error al cargar la lista:', error);
    } finally {
      setLoading(false);
    }
  };

  // Se ejecuta cada vez que cambia la página o la búsqueda
  useEffect(() => {
    fetchUsers();
  }, [page, search]);

  // Manejador para abrir modal de confirmación
  const handleOpenDeleteModal = (user) => {
    setSelectedUser(user);
    setIsModalOpen(true);
  };

  // Confirmar eliminación contra la API
  const handleConfirmDelete = async () => {
    if (!selectedUser) return;
    setDeleting(true);
    try {
      await api.delete(`/users/${selectedUser.id}`);
      setIsModalOpen(false);
      fetchUsers(); // Recargar la tabla
    } catch (error) {
      console.error('Error al eliminar:', error);
    } finally {
      setDeleting(false);
    }
  };

  return (
    <div className="table-container">
      <div className="table-header">
        <h3>Gestión de Usuarios</h3>
        
        {/* Filtro de Búsqueda enviado como parámetro a la API */}
        <input
          type="text"
          placeholder="Buscar por nombre o correo..."
          value={search}
          onChange={(e) => {
            setSearch(e.target.value);
            setPage(1); // Reiniciar a página 1 al buscar
          }}
          className="search-input"
        />
      </div>

      {loading ? (
        <div className="table-loading">Cargando registros...</div>
      ) : (
        <table className="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            {users.length > 0 ? (
              users.map((u) => (
                <tr key={u.id}>
                  <td>{u.id}</td>
                  <td>{u.name}</td>
                  <td>{u.email}</td>
                  <td><span className={`badge badge-${u.role}`}>{u.role}</span></td>
                  <td>
                    <button 
                      className="btn-delete" 
                      onClick={() => handleOpenDeleteModal(u)}
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="5" style={{ textAlign: 'center' }}>
                  No se encontraron resultados.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      )}

      {/* Paginación Servidor */}
      <div className="pagination">
        <button 
          disabled={page <= 1 || loading} 
          onClick={() => setPage(page - 1)}
        >
          Anterior
        </button>
        <span>Página {page} de {totalPages}</span>
        <button 
          disabled={page >= totalPages || loading} 
          onClick={() => setPage(page + 1)}
        >
          Siguiente
        </button>
      </div>

      {/* Modal de confirmación sin alert() */}
      <ConfirmModal
        isOpen={isModalOpen}
        title="¿Eliminar usuario?"
        message={`¿Estás seguro de eliminar a ${selectedUser?.name}?`}
        onConfirm={handleConfirmDelete}
        onCancel={() => setIsModalOpen(false)}
        loading={deleting}
      />
    </div>
  );
};