<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogCliente extends Model
{
    public $timestamps = false;

    protected $table = 'log_clientes';

    protected $fillable = [
        'cliente_id',
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

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
