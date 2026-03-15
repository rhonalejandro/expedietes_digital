<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Paciente
 *
 * Rol clínico vinculado a una Persona (patrón Persona → Paciente).
 * Usa SoftDeletes: destroy() marca deleted_at, no elimina físicamente.
 *
 * Campos propios:
 *   - persona_id (FK)
 *   - estado (boolean: activo/inactivo, independiente de deleted_at)
 */
class Paciente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'persona_id',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Retorna solo pacientes activos (estado = true, no eliminados). */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Nombre completo delegado a la Persona asociada. */
    public function getNombreCompletoAttribute(): string
    {
        return $this->persona
            ? trim($this->persona->nombre . ' ' . $this->persona->apellido)
            : '—';
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function casos()
    {
        return $this->hasMany(Caso::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
