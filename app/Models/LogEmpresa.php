<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEmpresa extends Model
{
    public $timestamps = false;

    protected $table = 'log_empresas';

    protected $fillable = [
        'empresa_id',
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

    public function empresa()  { return $this->belongsTo(Empresa::class); }
    public function usuario()  { return $this->belongsTo(Usuario::class); }
    public function sucursal() { return $this->belongsTo(Sucursal::class); }
}
