<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogCita extends Model
{
    public $timestamps = false;

    protected $table = 'log_citas';

    protected $fillable = [
        'cita_id',
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

    public function cita()    { return $this->belongsTo(Cita::class); }
    public function usuario() { return $this->belongsTo(Usuario::class); }
}
