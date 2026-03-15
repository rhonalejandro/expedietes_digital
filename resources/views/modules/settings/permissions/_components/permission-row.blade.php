{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Componente: permission-row.blade.php (< 50 líneas)
Propósito: Renderizar una fila de permiso en la tabla
--}}

@props(['permiso', 'modulo'])

<tr>
    <td>
        <span class="badge bg-primary bg-opacity-10 text-primary">
            {{ ucfirst($modulo) }}
        </span>
    </td>
    <td>
        <code>{{ $permiso->codigo }}</code>
    </td>
    <td>{{ $permiso->nombre }}</td>
    <td>
        @if($permiso->tipo === 'general')
            <span class="badge bg-info">General</span>
        @else
            <span class="badge bg-warning text-dark">Granular</span>
        @endif
    </td>
    <td>
        <span class="badge {{ $permiso->estado ? 'bg-success' : 'bg-secondary' }}">
            {{ $permiso->estado ? 'Activo' : 'Inactivo' }}
        </span>
    </td>
    <td class="text-end">
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('settings.permissions.edit', $permiso->id) }}" 
               class="btn btn-outline-primary" 
               title="Editar">
                <i class="ti ti-edit"></i>
            </a>
            
            <button type="button" 
                    class="btn btn-outline-{{ $permiso->estado ? 'warning' : 'success' }}"
                    onclick="togglePermissionStatus({{ $permiso->id }})"
                    title="{{ $permiso->estado ? 'Desactivar' : 'Activar' }}">
                <i class="ti ti-{{ $permiso->estado ? 'eye-off' : 'eye' }}"></i>
            </button>
            
            <button type="button" 
                    class="btn btn-outline-danger"
                    onclick="deletePermission({{ $permiso->id }})"
                    title="Eliminar">
                <i class="ti ti-trash"></i>
            </button>
        </div>
    </td>
</tr>
