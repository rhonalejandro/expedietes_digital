<div class="citas-leyenda">
    <span style="font-size:.72rem;font-weight:600;color:#8a94a6;text-transform:uppercase;letter-spacing:.05em;">Estado:</span>
    @foreach([
        'pendiente'  => ['color' => '#667eea', 'label' => 'Pendiente'],
        'confirmada' => ['color' => '#38a169', 'label' => 'Confirmada'],
        'atendida'   => ['color' => '#718096', 'label' => 'Atendida'],
        'cancelada'  => ['color' => '#e53e3e', 'label' => 'Cancelada'],
        'no_asistio' => ['color' => '#dd6b20', 'label' => 'No asistió'],
    ] as $cfg)
    <span class="citas-leyenda__item">
        <span class="citas-leyenda__dot" style="background:{{ $cfg['color'] }};"></span>
        {{ $cfg['label'] }}
    </span>
    @endforeach
</div>
