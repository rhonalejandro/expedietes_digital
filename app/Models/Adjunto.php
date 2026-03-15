<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Adjunto
 * Representa un archivo adjunto a una consulta
 */
class Adjunto extends Model
{
    protected $table = 'adjuntos';
    protected $fillable = [
        'consulta_id', 'tipo', 'ruta', 'descripcion'
    ];

    // Relación muchos a uno con Consulta
    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }
}
