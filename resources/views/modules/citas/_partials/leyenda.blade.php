<div class="citas-leyenda">
    @php
        $coloresEmpresa = \App\Models\Empresa::first()?->colores_estatus ?? \App\Models\Empresa::COLORES_DEFAULT;
        $estadosLeyenda = [
            'pendiente'   => 'Pendiente',
            'confirmada'  => 'Confirmada',
            'en_consulta' => 'En Consulta',
            'atendida'    => 'Atendida',
            'cancelada'   => 'Cancelada',
            'no_asistio'  => 'No asistió',
        ];
    @endphp
    <span style="font-size:.72rem;font-weight:600;color:#8a94a6;text-transform:uppercase;letter-spacing:.05em;">Estado:</span>
    @foreach($estadosLeyenda as $key => $label)
    <span class="citas-leyenda__item">
        <span class="citas-leyenda__dot" style="background:{{ $coloresEmpresa[$key] ?? '#64748b' }};"></span>
        {{ $label }}
    </span>
    @endforeach
</div>
