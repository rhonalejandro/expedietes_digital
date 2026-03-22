<div class="atc-card">
    <div class="atc-card-header">
        <i class="ti ti-history me-2"></i> Historial de Casos
        <span class="atc-badge-count">{{ $casos->count() }}</span>
    </div>
    <div class="atc-card-body p-0">

        @if($casos->isEmpty())
            <div class="atc-empty-hist">
                <i class="ti ti-folder-off"></i>
                <p>Sin historial previo</p>
            </div>
        @else
            <div class="accordion atc-accordion" id="accordionCasos">
                @foreach($casos as $idx => $caso)
                <div class="atc-accordion-item">
                    <button class="atc-accordion-btn {{ $idx > 0 ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#caso-{{ $caso->id }}"
                            aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}">
                        <div class="atc-acc-left">
                            <span class="atc-acc-dot atc-acc-dot--{{ $caso->estado }}"></span>
                            <div>
                                <div class="atc-acc-title">{{ \Illuminate\Support\Str::limit($caso->motivo ?? 'Caso #'.$caso->id, 40) }}</div>
                                <div class="atc-acc-sub">{{ \Carbon\Carbon::parse($caso->fecha_apertura)->isoFormat('D MMM YYYY') }} · {{ $caso->consultas->count() }} consulta(s)</div>
                            </div>
                        </div>
                        <i class="ti ti-chevron-down atc-acc-chevron"></i>
                    </button>

                    <div id="caso-{{ $caso->id }}" class="collapse {{ $idx === 0 ? 'show' : '' }}">
                        <div class="atc-acc-body">
                            @if($caso->notas_iniciales)
                                <p class="atc-hist-notas">{{ $caso->notas_iniciales }}</p>
                            @endif

                            @forelse($caso->consultas as $consulta)
                                <div class="atc-consulta-item">
                                    <div class="atc-consulta-fecha">
                                        <i class="ti ti-calendar-event"></i>
                                        {{ \Carbon\Carbon::parse($consulta->fecha_hora)->isoFormat('D MMM YYYY, HH:mm') }}
                                    </div>
                                    @if($consulta->diagnostico)
                                        <div class="atc-consulta-field">
                                            <span class="atc-field-lbl">Diagnóstico:</span>
                                            {{ $consulta->diagnostico }}
                                        </div>
                                    @endif
                                    @if($consulta->tratamiento)
                                        <div class="atc-consulta-field">
                                            <span class="atc-field-lbl">Tratamiento:</span>
                                            {{ $consulta->tratamiento }}
                                        </div>
                                    @endif
                                    @if(!empty($consulta->zonas_afectadas))
                                        @php
                                            $zonaLabels = [
                                                'talon'=>'Talón','planta'=>'Planta','arco'=>'Arco','antepi'=>'Antepié',
                                                'dedo_1'=>'D1','dedo_2'=>'D2','dedo_3'=>'D3','dedo_4'=>'D4','dedo_5'=>'D5',
                                                'uña_1'=>'Uña1','uña_2'=>'Uña2','uña_3'=>'Uña3','uña_4'=>'Uña4','uña_5'=>'Uña5',
                                            ];
                                        @endphp
                                        <div class="atc-consulta-field mt-1">
                                            <span class="atc-field-lbl">Zonas:</span>
                                            @foreach(['izquierdo','derecho'] as $side)
                                                @if(!empty($consulta->zonas_afectadas[$side]))
                                                    <span class="badge bg-light text-secondary border me-1" style="font-size:.68rem;">
                                                        {{ $side === 'izquierdo' ? 'Izq.' : 'Der.' }}:
                                                        {{ implode(', ', array_map(fn($z) => $zonaLabels[$z] ?? $z, $consulta->zonas_afectadas[$side])) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($consulta->adjuntos->count())
                                        <div class="atc-consulta-fotos">
                                            @foreach($consulta->adjuntos->where('tipo','imagen') as $adj)
                                                <a href="{{ Storage::url($adj->ruta) }}" target="_blank" title="{{ $adj->descripcion }}">
                                                    <img src="{{ Storage::url($adj->ruta) }}" alt="{{ $adj->descripcion }}">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Sin consultas registradas en este caso.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
