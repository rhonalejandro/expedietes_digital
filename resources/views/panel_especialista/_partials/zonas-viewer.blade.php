{{--
  Visor read-only del mapa podológico.
  Recibe: $zonasAfectadas (array con keys 'derecho' e 'izquierdo', cada uno array de zonas)
--}}
@php
$hotspots = [
    'derecho' => [
        ['zona'=>'dedo_1',  'label'=>'Hallux',        'sm'=>true,  'top'=>'8.8%',  'left'=>'25.7%'],
        ['zona'=>'dedo_2',  'label'=>'Índice',         'sm'=>true,  'top'=>'9.6%',  'left'=>'17.6%'],
        ['zona'=>'dedo_3',  'label'=>'Medio',          'sm'=>true,  'top'=>'13%',   'left'=>'12.2%'],
        ['zona'=>'dedo_4',  'label'=>'Anular',         'sm'=>true,  'top'=>'18.4%', 'left'=>'7.5%'],
        ['zona'=>'dedo_5',  'label'=>'Meñique',        'sm'=>true,  'top'=>'25.7%', 'left'=>'4.1%'],
        ['zona'=>'antepi',  'label'=>'Antepié',        'sm'=>false, 'top'=>'33%',   'left'=>'18.6%'],
        ['zona'=>'planta',  'label'=>'Planta',         'sm'=>false, 'top'=>'61%',   'left'=>'24.4%'],
        ['zona'=>'arco',    'label'=>'Arco plantar',   'sm'=>false, 'top'=>'51.4%', 'left'=>'11.2%'],
        ['zona'=>'talon',   'label'=>'Talón',          'sm'=>false, 'top'=>'87%',   'left'=>'19%'],
        ['zona'=>'tobillo', 'label'=>'Tobillo',        'sm'=>false, 'top'=>'5.9%',  'left'=>'73.2%'],
        ['zona'=>'dorso',   'label'=>'Dorso del pie',  'sm'=>false, 'top'=>'41%',   'left'=>'76.8%'],
        ['zona'=>'uña_1',   'label'=>'Uña Hallux',     'sm'=>true,  'top'=>'95.1%', 'left'=>'90.6%'],
        ['zona'=>'uña_2',   'label'=>'Uña Índice',     'sm'=>true,  'top'=>'94.4%', 'left'=>'81.1%'],
        ['zona'=>'uña_3',   'label'=>'Uña Medio',      'sm'=>true,  'top'=>'89%',   'left'=>'76.1%'],
        ['zona'=>'uña_4',   'label'=>'Uña Anular',     'sm'=>true,  'top'=>'83.6%', 'left'=>'71.4%'],
        ['zona'=>'uña_5',   'label'=>'Uña Meñique',    'sm'=>true,  'top'=>'77.1%', 'left'=>'67.6%'],
    ],
    'izquierdo' => [
        ['zona'=>'tobillo', 'label'=>'Tobillo',        'sm'=>false, 'top'=>'8.4%',  'left'=>'26.6%'],
        ['zona'=>'dorso',   'label'=>'Dorso del pie',  'sm'=>false, 'top'=>'41.6%', 'left'=>'21%'],
        ['zona'=>'uña_5',   'label'=>'Uña Meñique',    'sm'=>true,  'top'=>'76.4%', 'left'=>'33.2%'],
        ['zona'=>'uña_4',   'label'=>'Uña Anular',     'sm'=>true,  'top'=>'93.8%', 'left'=>'18.8%'],
        ['zona'=>'uña_3',   'label'=>'Uña Medio',      'sm'=>true,  'top'=>'89.1%', 'left'=>'23.9%'],
        ['zona'=>'uña_2',   'label'=>'Uña Índice',     'sm'=>true,  'top'=>'84.7%', 'left'=>'29.5%'],
        ['zona'=>'uña_1',   'label'=>'Uña Hallux',     'sm'=>true,  'top'=>'94.7%', 'left'=>'9.3%'],
        ['zona'=>'dedo_5',  'label'=>'Meñique',        'sm'=>true,  'top'=>'25.5%', 'left'=>'96%'],
        ['zona'=>'dedo_4',  'label'=>'Anular',         'sm'=>true,  'top'=>'18.6%', 'left'=>'92.6%'],
        ['zona'=>'dedo_3',  'label'=>'Medio',          'sm'=>true,  'top'=>'13.6%', 'left'=>'88.2%'],
        ['zona'=>'dedo_2',  'label'=>'Índice',         'sm'=>true,  'top'=>'9.5%',  'left'=>'82.6%'],
        ['zona'=>'dedo_1',  'label'=>'Hallux',         'sm'=>true,  'top'=>'7%',    'left'=>'75.5%'],
        ['zona'=>'antepi',  'label'=>'Antepié',        'sm'=>false, 'top'=>'33.8%', 'left'=>'80.5%'],
        ['zona'=>'planta',  'label'=>'Planta',         'sm'=>false, 'top'=>'61%',   'left'=>'74.7%'],
        ['zona'=>'arco',    'label'=>'Arco plantar',   'sm'=>false, 'top'=>'52.2%', 'left'=>'91.3%'],
        ['zona'=>'talon',   'label'=>'Talón',          'sm'=>false, 'top'=>'87%',   'left'=>'79%'],
    ],
];

$tieneDerecho   = !empty($zonasAfectadas['derecho']);
$tieneIzquierdo = !empty($zonasAfectadas['izquierdo']);
@endphp

<div class="zv-wrap">
    @foreach(['derecho','izquierdo'] as $pie)
    @php $seleccionadas = $zonasAfectadas[$pie] ?? []; @endphp
    @if(!empty($seleccionadas))
    <div class="zv-pie">
        <div class="zv-pie-titulo">
            @if($pie === 'derecho') Pie Derecho <i class="ti ti-arrow-right"></i>
            @else <i class="ti ti-arrow-left"></i> Pie Izquierdo
            @endif
        </div>
        <div class="pie-img-wrapper" style="pointer-events:none;user-select:none;">
            <img src="{{ asset('assets/Pies/pie-' . $pie . '.png') }}"
                 alt="Pie {{ ucfirst($pie) }}" class="pie-img">
            @foreach($hotspots[$pie] as $h)
            <span class="zona-hotspot {{ $h['sm'] ? 'hotspot-sm' : '' }} {{ in_array($h['zona'], $seleccionadas) ? 'activo' : 'zv-inactivo' }}"
                  data-label="{{ $h['label'] }}"
                  style="top:{{ $h['top'] }};left:{{ $h['left'] }}"></span>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach
</div>
