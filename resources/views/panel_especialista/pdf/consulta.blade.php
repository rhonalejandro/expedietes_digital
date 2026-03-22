@extends('panel_especialista.pdf.layout')

@section('body')
@php
    $persona   = $paciente->persona;
    $edad      = $persona->fecha_nacimiento
        ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->age
        : null;
    $zonaLabels = [
        'talon'=>'Talón','planta'=>'Planta','arco'=>'Arco plantar','antepi'=>'Antepié',
        'tobillo'=>'Tobillo','dorso'=>'Dorso del pie',
        'dedo_1'=>'Hallux','dedo_2'=>'Índice','dedo_3'=>'Medio','dedo_4'=>'Anular','dedo_5'=>'Meñique',
        'uña_1'=>'Uña Hallux','uña_2'=>'Uña Índice','uña_3'=>'Uña Medio','uña_4'=>'Uña Anular','uña_5'=>'Uña Meñique',
    ];
    $fotos = $fotosBase64 ?? collect();
@endphp

{{-- ── Bloque de título del documento ──────────────────────── --}}
<div style="margin-bottom:16px;text-align:center">
    <div style="font-size:15pt;font-weight:bold;color:#1a202c;letter-spacing:-.02em;margin-bottom:2px">
        Ficha Médica de Consulta
    </div>
    <div style="font-size:7.5pt;color:#64748b">
        {{ \Carbon\Carbon::parse($consulta->fecha_hora)->isoFormat('dddd D [de] MMMM [de] YYYY') }}
        &nbsp;·&nbsp;
        {{ \Carbon\Carbon::parse($consulta->fecha_hora)->format('H:i') }}
        &nbsp;·&nbsp; Caso #{{ $caso->id }}
    </div>
</div>

{{-- ── Tarjeta del caso ─────────────────────────────────────── --}}
<div style="background:#f3f7f1;border:1px solid #b8d1b0;border-radius:5px;padding:9px 13px;margin-bottom:16px">
    <div style="font-size:8.5pt;margin-bottom:6px;color:#1a202c">
        <strong style="color:#4a6e3a">Motivo del caso:</strong>&nbsp;{{ $caso->motivo ?? 'Consulta general' }}
    </div>
    <table width="100%" style="font-size:7.5pt;color:#475569">
        <tr>
            <td>
                <strong>Estado:</strong>&nbsp;<span class="{{ $caso->estado === 'abierto' ? 'pill' : 'pill pill-rojo' }}">{{ ucfirst($caso->estado) }}</span>
            </td>
            <td>
                <strong>Apertura del caso:</strong>&nbsp;{{ \Carbon\Carbon::parse($caso->fecha_apertura)->format('d/m/Y') }}
            </td>
            <td align="right">
                <strong>Paciente:</strong>&nbsp;{{ $paciente->nombre_completo }}
            </td>
        </tr>
    </table>
</div>

{{-- ── Datos del paciente ───────────────────────────────────── --}}
<div class="seccion">
    <div class="seccion-titulo">Datos del Paciente</div>
    <table class="tabla-datos">
        <tr>
            <td class="lbl">Nombre completo</td>
            <td class="val">{{ $paciente->nombre_completo }}</td>
            <td class="lbl">Identificación</td>
            <td class="val">{{ $persona->tipo_identificacion ? $persona->tipo_identificacion.' ' : '' }}{{ $persona->identificacion ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Fecha de nacimiento</td>
            <td class="val">
                {{ $persona->fecha_nacimiento ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') : '—' }}
                @if($edad) ({{ $edad }} años) @endif
            </td>
            <td class="lbl">Género</td>
            <td class="val">{{ ucfirst($persona->genero ?? '—') }}</td>
        </tr>
        @if($persona->nacionalidad || $persona->ocupacion)
        <tr>
            <td class="lbl">Nacionalidad</td>
            <td class="val">{{ $persona->nacionalidad ?? '—' }}</td>
            <td class="lbl">Ocupación</td>
            <td class="val">{{ $persona->ocupacion ?? '—' }}</td>
        </tr>
        @endif
        @if($persona->seguro_medico)
        <tr>
            <td class="lbl">Seguro médico</td>
            <td class="val" colspan="3">{{ $persona->seguro_medico }}</td>
        </tr>
        @endif
    </table>
</div>

{{-- ── Notas iniciales del caso ────────────────────────────── --}}
@if($caso->notas_iniciales)
<div class="seccion">
    <div class="seccion-titulo">Notas Iniciales del Caso</div>
    <div class="caja">
        <div class="caja-body">{!! $caso->notas_iniciales !!}</div>
    </div>
</div>
@endif

{{-- ── Zonas podológicas afectadas ─────────────────────────── --}}
@if(!empty($consulta->zonas_afectadas))
<div class="seccion">
    <div class="seccion-titulo">Zonas Podológicas Afectadas</div>
    <div style="padding:3px 0;">
        @foreach(['derecho','izquierdo'] as $side)
            @if(!empty($consulta->zonas_afectadas[$side]))
                <strong style="font-size:8pt">Pie {{ $side === 'derecho' ? 'Derecho' : 'Izquierdo' }}:</strong>
                @foreach($consulta->zonas_afectadas[$side] as $z)
                    <span class="pill">{{ $zonaLabels[$z] ?? $z }}</span>
                @endforeach
                &nbsp;&nbsp;
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- ── Registro clínico ─────────────────────────────────────── --}}
<div class="seccion">
    <div class="seccion-titulo">Registro Clínico</div>

    @if($consulta->observaciones)
    <div class="caja">
        <div class="caja-titulo">Observaciones</div>
        <div class="caja-body">{!! $consulta->observaciones !!}</div>
    </div>
    @endif

    @if($consulta->diagnostico)
    <div class="caja">
        <div class="caja-titulo">Diagnóstico</div>
        <div class="caja-body">{!! $consulta->diagnostico !!}</div>
    </div>
    @endif

    @if($consulta->tratamiento)
    <div class="caja">
        <div class="caja-titulo">Tratamiento</div>
        <div class="caja-body">{!! $consulta->tratamiento !!}</div>
    </div>
    @endif

    @if($consulta->indicaciones)
    <div class="caja">
        <div class="caja-titulo">Indicaciones al Paciente</div>
        <div class="caja-body">{!! $consulta->indicaciones !!}</div>
    </div>
    @endif
</div>

{{-- ── Receta ───────────────────────────────────────────────── --}}
@if($consulta->receta)
<div class="seccion" style="page-break-inside:avoid">
    <div class="seccion-titulo">Receta Médica</div>
    <div class="caja" style="border-left-color:#059669">
        <div class="caja-body">{!! $consulta->receta !!}</div>
    </div>
</div>
@endif

{{-- ── Firma ────────────────────────────────────────────────── --}}
<div class="firma-wrap">
    <table>
        <tr>
            <td style="width:55%"></td>
            <td style="text-align:center">
                @if($firmaBase64 ?? null)
                    <img src="{{ $firmaBase64 }}" style="max-height:55px;max-width:160px;margin-bottom:4px"><br>
                @else
                    <div style="height:44px"></div>
                @endif
                <div class="firma-linea" style="margin:0 auto 4px"></div>
                <div class="firma-nombre">{{ $especialista->nombre_completo }}</div>
                @if($especialista->profesion)
                    <div class="firma-cargo">{{ $especialista->profesion }}</div>
                @endif
                @if($especialista->especialidad)
                    <div class="firma-cargo">{{ $especialista->especialidad }}</div>
                @endif
                @if($especialista->num_colegiado)
                    <div class="firma-cargo">Colegiado N° {{ $especialista->num_colegiado }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- ── Anexos fotográficos ──────────────────────────────────── --}}
@if($fotos->isNotEmpty())
<div class="seccion" style="page-break-before:always">
    <div class="seccion-titulo">Anexos — Registro Fotográfico</div>
    @foreach($fotos as $foto)
    <div style="text-align:center;margin-bottom:14px;page-break-inside:avoid">
        <img src="{{ $foto['src'] }}"
             style="width:80%;border-radius:6px;border:1px solid #dde8da;display:block;margin:0 auto">
        @if($foto['descripcion'])
            <div style="font-size:7pt;color:#64748b;margin-top:4px">{{ $foto['descripcion'] }}</div>
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
