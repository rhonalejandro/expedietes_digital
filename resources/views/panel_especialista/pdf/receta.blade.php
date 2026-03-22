@extends('panel_especialista.pdf.layout')

@section('body')
@php
    $persona = $paciente->persona;
    $edad    = $persona->fecha_nacimiento
        ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->age
        : null;
@endphp


{{-- ── Título + fecha ──────────────────────────────────────── --}}
<div style="text-align:center;margin-bottom:16px">
    <div style="font-size:15pt;font-weight:bold;color:#1a202c;letter-spacing:-.02em;margin-bottom:2px">
        Receta Médica
    </div>
    <div style="font-size:7.5pt;color:#64748b">
        {{ \Carbon\Carbon::parse($consulta->fecha_hora)->isoFormat('dddd D [de] MMMM [de] YYYY') }}
        &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($consulta->fecha_hora)->format('H:i') }}
    </div>
</div>

{{-- ── Datos del paciente ───────────────────────────────────── --}}
<div style="background:#f3f7f1;border:1px solid #b8d1b0;border-radius:5px;padding:8px 13px;margin-bottom:16px">
    <table width="100%" style="font-size:8pt">
        <tr>
            <td><strong style="color:#4a6e3a">Paciente:</strong>&nbsp;{{ $paciente->nombre_completo }}</td>
            @if($edad)
            <td><strong style="color:#4a6e3a">Edad:</strong>&nbsp;{{ $edad }} años</td>
            @endif
            @if($persona->identificacion)
            <td align="right"><strong style="color:#4a6e3a">Cédula / ID:</strong>&nbsp;{{ $persona->identificacion }}</td>
            @endif
        </tr>
    </table>
</div>

{{-- ── Receta ───────────────────────────────────────────────── --}}
@if($consulta->receta)
<div class="seccion">
    <div class="seccion-titulo">Medicamentos / Prescripción</div>
    <div style="min-height:180px;border:1px solid #dde8da;border-radius:5px;padding:12px 14px;background:#fff;font-size:9pt">
        {!! $consulta->receta !!}
    </div>
</div>
@endif

{{-- ── Indicaciones ─────────────────────────────────────────── --}}
@if($consulta->indicaciones)
<div class="seccion" style="margin-top:14px">
    <div class="seccion-titulo">Indicaciones al Paciente</div>
    <div style="border:1px solid #dde8da;border-left:3px solid #6b9158;border-radius:5px;padding:10px 14px;background:#f8faf7;font-size:9pt">
        {!! $consulta->indicaciones !!}
    </div>
</div>
@endif

{{-- ── Firma ────────────────────────────────────────────────── --}}
<div class="firma-wrap" style="margin-top:36px">
    <table>
        <tr>
            <td style="width:50%">
                <div style="font-size:7.5pt;color:#94a3b8">
                    Documento generado el {{ now()->isoFormat('D [de] MMMM [de] YYYY') }}
                </div>
            </td>
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

@endsection
