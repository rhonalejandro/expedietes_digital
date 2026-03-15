<div class="citas-toolbar d-flex flex-wrap align-items-center gap-3">

    {{-- Filtro especialistas --}}
    <div class="esp-filtro-wrap flex-grow-1">
        <span class="me-1" style="font-size:.75rem;font-weight:600;color:#8a94a6;text-transform:uppercase;letter-spacing:.05em;">Especialistas:</span>

        <button class="esp-filtro-btn" data-id="todos">
            Todos
        </button>

        @foreach($especialistas as $esp)
        <button class="esp-filtro-btn" data-id="{{ $esp->id }}">
            <span class="esp-avatar-xs">
                {{ strtoupper(substr($esp->persona->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($esp->persona->apellido ?? '', 0, 1)) }}
            </span>
            {{ $esp->nombre_completo }}
        </button>
        @endforeach
    </div>

    {{-- Botón nueva cita --}}
    <button type="button" class="btn btn-sm btn-primary ms-auto"
            data-bs-toggle="modal" data-bs-target="#modal-crear-cita">
        <i class="ti ti-plus me-1"></i>Nueva Cita
    </button>

</div>
