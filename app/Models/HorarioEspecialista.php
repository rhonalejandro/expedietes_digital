<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioEspecialista extends Model
{
    protected $table = 'horarios_especialistas';

    protected $fillable = [
        'especialista_id',
        'sucursal_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'duracion_cita',
        'citas_maximas',
    ];

    public function especialista()
    {
        return $this->belongsTo(Especialista::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
