<div class="row g-3">

    <div class="col-12">
        <label class="srv-form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="form-control form-control-sm"
               placeholder="Ej: Consulta Podológica General" required>
    </div>

    <div class="col-12">
        <label class="srv-form-label">Descripción</label>
        <textarea name="descripcion" class="form-control form-control-sm" rows="3"
                  placeholder="Descripción breve del servicio..."></textarea>
    </div>

    <div class="col-12">
        <label class="srv-form-label">Precio (USD) <span class="text-danger">*</span></label>
        <div class="input-group input-group-sm">
            <span class="input-group-text">$</span>
            <input type="number" name="precio" class="form-control" step="0.01" min="0"
                   placeholder="0.00" required>
        </div>
    </div>

</div>
