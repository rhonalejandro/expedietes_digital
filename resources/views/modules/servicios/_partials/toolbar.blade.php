<div class="card border-0 srv-toolbar mb-4">
    <div class="card-body py-3 px-4 d-flex flex-wrap align-items-center gap-3">

        <form method="GET" action="{{ route('servicios.index') }}" class="d-flex gap-2 flex-grow-1" style="max-width:420px;">
            <input type="text" name="q" value="{{ request('q') }}"
                   class="form-control form-control-sm" placeholder="Buscar servicio...">
            <select name="estado" class="form-select form-select-sm" style="width:130px;">
                <option value="">Todos</option>
                <option value="1" @selected(request('estado') === '1')>Activos</option>
                <option value="0" @selected(request('estado') === '0')>Inactivos</option>
            </select>
            <button type="submit" class="btn btn-sm btn-light">
                <i class="ti ti-search"></i>
            </button>
            @if(request('q') || request()->has('estado'))
                <a href="{{ route('servicios.index') }}" class="btn btn-sm btn-light">
                    <i class="ti ti-x"></i>
                </a>
            @endif
        </form>

        <button type="button" class="btn btn-sm btn-primary ms-auto"
                data-bs-toggle="modal" data-bs-target="#modal-crear-servicio">
            <i class="ti ti-plus me-1"></i>Nuevo Servicio
        </button>

    </div>
</div>
