<div class="card border-0 srv-table-card">
    <div class="table-responsive">
        <table class="table srv-table mb-0">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Descripción</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servicios as $servicio)
                <tr data-row-id="{{ $servicio->id }}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="srv-icon">
                                <i class="ti ti-medical-cross"></i>
                            </div>
                            <span class="fw-medium">{{ $servicio->nombre }}</span>
                        </div>
                    </td>
                    <td class="text-muted" style="max-width:320px;">
                        <span class="text-truncate d-block" style="max-width:300px;" title="{{ $servicio->descripcion }}">
                            {{ $servicio->descripcion ?: '—' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <span class="srv-precio">{{ $servicio->precio_formateado }}</span>
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-flex m-0">
                            <input class="form-check-input srv-toggle" type="checkbox"
                                   data-id="{{ $servicio->id }}"
                                   @checked($servicio->estado)>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button class="srv-action-btn" title="Editar"
                                    data-action="editar" data-id="{{ $servicio->id }}">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button class="srv-action-btn srv-action-btn--delete" title="Eliminar"
                                    data-action="eliminar"
                                    data-id="{{ $servicio->id }}"
                                    data-nombre="{{ $servicio->nombre }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted" style="font-size:.85rem;">
                        <i class="ti ti-inbox d-block mb-2" style="font-size:2rem;color:#cbd5e0;"></i>
                        No hay servicios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($servicios->hasPages())
    <div class="card-footer border-top bg-transparent px-4 py-3">
        {{ $servicios->links() }}
    </div>
    @endif
</div>
