/**
 * Settings Module JavaScript
 * Lógica específica para el módulo de configuración
 */

(function() {
    'use strict';

    /**
     * Settings Module
     * Maneja la lógica del módulo de configuración
     */
    const SettingsModule = {
        
        /**
         * Inicializar módulo
         */
        init: function() {
            this.bindEvents();
            this.initTooltips();
        },

        /**
         * Vincular eventos
         */
        bindEvents: function() {
            // Eventos delegados para mejor performance
            document.addEventListener('DOMContentLoaded', () => {
                this.initFormValidation();
                this.initModals();
            });
        },

        /**
         * Inicializar validación de formularios
         */
        initFormValidation: function() {
            const forms = document.querySelectorAll('.settings-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        },

        /**
         * Inicializar modals
         */
        initModals: function() {
            // Limpiar formularios de modals al cerrar
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', () => {
                    const form = modal.querySelector('form');
                    if (form) {
                        form.reset();
                        form.classList.remove('was-validated');
                    }
                });
            });
        },

        /**
         * Inicializar tooltips
         */
        initTooltips: function() {
            // Bootstrap tooltips se inicializan automáticamente
            // Aquí podemos agregar custom tooltips si es necesario
        },

        /**
         * Mostrar notificación toast
         * @param {string} message - Mensaje a mostrar
         * @param {string} type - Tipo de notificación (success, error, warning, info)
         */
        showToast: function(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            
            if (!toastContainer) {
                console.warn('Toast container not found');
                return;
            }

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remover después de cerrar
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        },

        /**
         * Confirmar acción
         * @param {string} message - Mensaje de confirmación
         * @returns {Promise<boolean>}
         */
        confirm: function(message) {
            return new Promise((resolve) => {
                if (confirm(message)) {
                    resolve(true);
                } else {
                    resolve(false);
                }
            });
        },

        /**
         * Manejar respuesta de AJAX
         * @param {Response} response
         * @returns {Promise}
         */
        handleResponse: async function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.showToast(data.message || 'Operación exitosa', 'success');
            } else {
                this.showToast(data.message || 'Error en la operación', 'error');
            }
            
            return data;
        },

        /**
         * Prevenir doble submit
         * @param {HTMLFormElement} form
         */
        preventDoubleSubmit: function(form) {
            let submitting = false;
            
            form.addEventListener('submit', (e) => {
                if (submitting) {
                    e.preventDefault();
                    return false;
                }
                submitting = true;
                
                const submitButtons = form.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(btn => {
                    btn.disabled = true;
                    if (btn.innerHTML.includes('Guardando')) {
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
                    }
                });
            });
        }
    };

    /**
     * Sucursales Module
     * Maneja la lógica específica de sucursales
     */
    const SucursalesModule = {
        
        /**
         * Inicializar módulo
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Vincular eventos
         */
        bindEvents: function() {
            window.editSucursal = this.editSucursal.bind(this);
            window.toggleSucursal = this.toggleSucursal.bind(this);
        },

        /**
         * Editar sucursal
         * @param {Object} sucursal - Datos de la sucursal
         */
        editSucursal: function(sucursal) {
            // Llenar formulario modal
            document.getElementById('edit_sucursal_id').value = sucursal.id;
            document.getElementById('edit_nombre').value = sucursal.nombre;
            document.getElementById('edit_direccion').value = sucursal.direccion;
            document.getElementById('edit_telefono').value = sucursal.telefono || sucursal.contacto || '';
            document.getElementById('edit_email').value = sucursal.email || '';
            document.getElementById('edit_encargado').value = sucursal.encargado || '';
            document.getElementById('edit_estado').checked = sucursal.estado;
            
            // Actualizar acción del formulario
            document.getElementById('editSucursalForm').action = `/settings/sucursal/${sucursal.id}`;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('editSucursalModal'));
            modal.show();
        },

        /**
         * Cambiar estado de sucursal
         * @param {number} id - ID de la sucursal
         */
        toggleSucursal: function(id) {
            if (confirm('¿Cambiar estado de esta sucursal?')) {
                window.location.href = `/settings/sucursal/${id}/toggle`;
            }
        }
    };

    // Inicializar módulos
    SettingsModule.init();
    SucursalesModule.init();

    // Exponer globalmente si es necesario
    window.SettingsModule = SettingsModule;
    window.SucursalesModule = SucursalesModule;

})();
