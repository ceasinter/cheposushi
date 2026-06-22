<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleDelivery extends Model
{
    protected $fillable = [
        'pedido_id', 'direccion', 'referencia', 'comuna',
        'repartidor_id', 'estado', 'asignado_at', 'entregado_at'
    ];

    protected $casts = [
        'asignado_at'  => 'datetime',
        'entregado_at' => 'datetime',
    ];

    const ESTADO_SIN_ASIGNAR = 'sin_asignar';
    const ESTADO_ASIGNADO    = 'asignado';
    const ESTADO_EN_CAMINO   = 'en_camino';
    const ESTADO_ENTREGADO   = 'entregado';

    public function pedido():BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function repartidor():BelongsTo
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }
}
