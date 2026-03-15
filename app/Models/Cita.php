<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Cita
 *
 * Representa una cita agendada. El paciente_id es nullable para permitir
 * leads desde Chatwoot u otros canales sin ficha registrada aún.
 */
class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'especialista_id',
        'paciente_id',
        'sucursal_id',
        'caso_id',
        'servicio_id',
        'nombre_lead',
        'telefono_lead',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estatus',
        'motivo',
        'observaciones',
        'origen',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopePendientes($q)  { return $q->where('estatus', 'pendiente'); }
    public function scopeConfirmadas($q) { return $q->where('estatus', 'confirmada'); }
    public function scopeDelDia($q, $fecha = null)
    {
        return $q->where('fecha', $fecha ?? now()->toDateString());
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /**
     * Nombre para mostrar: paciente registrado o nombre del lead.
     */
    public function getNombrePacienteAttribute(): string
    {
        if ($this->paciente) {
            return $this->paciente->persona->nombre . ' ' . $this->paciente->persona->apellido;
        }
        return $this->nombre_lead ?? 'Sin nombre';
    }

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function especialista()
    {
        return $this->belongsTo(Especialista::class, 'especialista_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function caso()
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function expediente()
    {
        return $this->hasOne(Expediente::class);
    }

    public function logs()
    {
        return $this->hasMany(LogCita::class, 'cita_id');
    }
}
