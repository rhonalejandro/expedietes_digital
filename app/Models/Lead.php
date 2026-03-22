<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'origen',
        'chatwoot_contact_id',
        'chatwoot_conv_id',
        'notas',
        'estatus',
        'convertido_en',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'convertido_en');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'telefono_lead', 'telefono');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->whereNotIn('estatus', ['convertido', 'descartado']);
    }

    public function scopePorEstatus($query, string $estatus)
    {
        return $query->where('estatus', $estatus);
    }
}
