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
        'caso_id', 'especialista_id', 'fecha_hora', 'estado', 'diagnostico', 'observaciones', 'tratamiento', 'receta', 'firma_digital'
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

    // Relación uno a muchos con Adjuntos
    public function adjuntos()
    {
        return $this->hasMany(Adjunto::class);
    }
}
