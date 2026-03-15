<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEspecialista extends Model
{
    public $timestamps = false;

    protected $table = 'log_especialistas';

    protected $fillable = [
        'especialista_id',
        'usuario_id',
        'tipo_accion',
        'fecha',
        'detalles',
        'sucursal_id',
    ];

    protected $casts = [
        'fecha'    => 'datetime',
        'detalles' => 'array',
    ];

    public function especialista()
    {
        return $this->belongsTo(Especialista::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
