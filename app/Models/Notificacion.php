<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Notificacion
 * Representa notificaciones enviadas a los usuarios
 */
class Notificacion extends Model
{
    // Relación muchos a uno con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
