<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Consulta
 * Representa una consulta médica asociada a un caso
 */
class Consulta extends Model
{
    protected $table = 'consultas';
    protected $fillable = [
        'caso_id', 'cita_id', 'especialista_id',
        'fecha_hora', 'estado',
        'observaciones', 'diagnostico', 'tratamiento', 'indicaciones', 'receta',
        'zonas_afectadas', 'firma_digital',
    ];

    protected $casts = [
        'zonas_afectadas' => 'array',
    ];

    // Relación muchos a uno con Caso
    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    // Relación muchos a uno con Especialista
    public function especialista()
    {
        return $this->belongsTo(Especialista::class, 'especialista_id');
    }

    // Relación muchos a uno con Cita
    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    // Relación uno a muchos con Adjuntos
    public function adjuntos()
    {
        return $this->hasMany(Adjunto::class);
    }
}
