import React from 'react';

export const ConfirmModal = ({ isOpen, title, message, onConfirm, onCancel, loading }) => {
  // Si el modal no está abierto, no renderiza nada
  if (!isOpen) return null;

  return (
    <div className="modal-overlay">
      <div className="modal-card">
        <h3 className="modal-title">{title || '¿Estás seguro?'}</h3>
        <p className="modal-message">{message || 'Esta acción no se puede deshacer.'}</p>
        
        <div className="modal-actions">
          <button 
            type="button" 
            onClick={onCancel} 
            className="btn-modal-cancel" 
            disabled={loading}
          >
            Cancelar
          </button>
          <button 
            type="button" 
            onClick={onConfirm} 
            className="btn-modal-confirm" 
            disabled={loading}
          >
            {loading ? 'Procesando...' : 'Confirmar'}
          </button>
        </div>
      </div>
    </div>
  );
};