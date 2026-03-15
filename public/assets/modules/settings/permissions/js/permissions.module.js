/**
 * Desarrollo - Permisos de Usuarios - UI - 2026-02-18
 * 
 * Script: permissions.module.js
 * 
 * Propósito: Lógica del módulo de permisos
 * 
 * Principios:
 * - Single Responsibility: Solo maneja permisos
 * - Event delegation
 * - Fetch API para AJAX
 * - Sin dependencias externas
 */

(function() {
    'use strict';

    // ================================
    // Configuración
    // ================================
    const CONFIG = {
        urls: {
            toggle: '/settings/permisos/{id}/toggle',
            delete: '/settings/permisos/{id}',
        },
        selectors: {
            form: '#permissionForm',
            modal: '#permissionModal',
            table: '.permissions-table',
        },
        messages: {
            confirmDelete: '¿Estás seguro de eliminar este permiso? Esta acción no se puede deshacer.',
            successToggle: 'Estado actualizado correctamente.',
            successDelete: 'Permiso eliminado correctamente.',
            error: 'Ha ocurrido un error. Inténtalo de nuevo.',
        },
    };

    // ================================
    // Utilidades
    // ================================
    const Utils = {
        /**
         * Obtener token CSRF
         */
        getCSRFToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        /**
         * Mostrar alerta
         */
        showAlert(message, type = 'success') {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'ti-check' : 'ti-x';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-custom mb-3" role="alert">
                    <i class="ti ${icon} me-2"></i>${message}
                </div>
            `;
            
            const container = document.querySelector('.container-fluid');
            container.insertAdjacentHTML('afterbegin', alertHtml);
            
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) alert.remove();
            }, 5000);
        },

        /**
         * Confirmar acción
         */
        confirm(message) {
            return new Promise((resolve) => {
                if (window.confirm(message)) {
                    resolve(true);
                } else {
                    resolve(false);
                }
            });
        },
    };

    // ================================
    // Módulo de Permisos
    // ================================
    const PermissionsModule = {
        /**
         * Inicializar módulo
         */
        init() {
            this.bindEvents();
            console.log('Permissions module initialized');
        },

        /**
         * Bind de eventos
         */
        bindEvents() {
            document.addEventListener('DOMContentLoaded', () => {
                this.initModals();
            });
        },

        /**
         * Inicializar modales
         */
        initModals() {
            const modalEl = document.getElementById('permissionModal');
            if (modalEl) {
                this.modal = new bootstrap.Modal(modalEl);
            }
        },

        /**
         * Toggle estado de permiso
         */
        async toggleStatus(permissionId) {
            try {
                const url = CONFIG.urls.toggle.replace('{id}', permissionId);
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': Utils.getCSRFToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await response.json();

                if (response.ok) {
                    Utils.showAlert(CONFIG.messages.successToggle);
                    setTimeout(() => location.reload(), 500);
                } else {
                    Utils.showAlert(data.message || CONFIG.messages.error, 'error');
                }
            } catch (error) {
                console.error('Error toggling permission:', error);
                Utils.showAlert(CONFIG.messages.error, 'error');
            }
        },

        /**
         * Eliminar permiso
         */
        async deletePermission(permissionId) {
            const confirmed = await Utils.confirm(CONFIG.messages.confirmDelete);
            
            if (!confirmed) return;

            try {
                const url = CONFIG.urls.delete.replace('{id}', permissionId);
                
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': Utils.getCSRFToken(),
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    Utils.showAlert(CONFIG.messages.successDelete);
                    setTimeout(() => location.reload(), 500);
                } else {
                    Utils.showAlert(data.message || CONFIG.messages.error, 'error');
                }
            } catch (error) {
                console.error('Error deleting permission:', error);
                Utils.showAlert(CONFIG.messages.error, 'error');
            }
        },

        /**
         * Abrir modal de edición
         */
        openEditModal(permissionData) {
            if (!this.modal) return;

            const form = document.getElementById('permissionForm');
            if (!form) return;

            // Setear valores
            form.querySelector('[name="modulo"]').value = permissionData.modulo;
            form.querySelector('[name="codigo"]').value = permissionData.codigo;
            form.querySelector('[name="nombre"]').value = permissionData.nombre;
            form.querySelector('[name="descripcion"]').value = permissionData.descripcion || '';
            form.querySelector('[name="tipo"]').value = permissionData.tipo;
            form.querySelector('[name="estado"]').checked = permissionData.estado;
            form.action = `/settings/permisos/${permissionData.id}`;

            // Actualizar título
            document.getElementById('modalTitle').textContent = 'Editar Permiso';

            this.modal.show();
        },

        /**
         * Eliminar plantilla
         */
        async deleteTemplate(templateId) {
            const confirmed = await Utils.confirm('¿Estás seguro de eliminar esta plantilla?');
            if (!confirmed) return;

            try {
                const url = `/settings/permisos/plantillas/${templateId}`;
                
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': Utils.getCSRFToken(),
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    Utils.showAlert('Plantilla eliminada correctamente.');
                    setTimeout(() => location.reload(), 500);
                } else {
                    Utils.showAlert(data.message || 'Error al eliminar', 'error');
                }
            } catch (error) {
                console.error('Error deleting template:', error);
                Utils.showAlert(CONFIG.messages.error, 'error');
            }
        },

        /**
         * Toggle estado de plantilla
         */
        async toggleTemplateStatus(templateId) {
            try {
                const url = `/settings/permisos/plantillas/${templateId}/toggle`;
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': Utils.getCSRFToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await response.json();

                if (response.ok) {
                    Utils.showAlert('Estado actualizado correctamente.');
                    setTimeout(() => location.reload(), 500);
                } else {
                    Utils.showAlert(data.message || 'Error al actualizar', 'error');
                }
            } catch (error) {
                console.error('Error toggling template:', error);
                Utils.showAlert(CONFIG.messages.error, 'error');
            }
        },
    };

    // ================================
    // Exponer funciones globales
    // ================================
    window.togglePermissionStatus = (id) => PermissionsModule.toggleStatus(id);
    window.deletePermission = (id) => PermissionsModule.deletePermission(id);
    window.deleteTemplate = (id) => PermissionsModule.deleteTemplate(id);
    window.toggleTemplateStatus = (id) => PermissionsModule.toggleTemplateStatus(id);

    // ================================
    // Inicializar
    // ================================
    PermissionsModule.init();
})();
