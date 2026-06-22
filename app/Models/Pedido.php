<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    protected $fillable = [
        'numero', 'tipo', 'estado', 'cliente_id',
        'nombre_cliente', 'telefono_cliente',
        'subtotal', 'descuento', 'total',
        'metodo_pago', 'monto_pagado', 'notas', 'usuario_id'
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'descuento'    => 'decimal:2',
        'total'        => 'decimal:2',
        'monto_pagado' => 'decimal:2',
    ];

    // Estados posibles como constantes
    const ESTADO_PENDIENTE       = 'pendiente';
    const ESTADO_EN_PREPARACION  = 'en_preparacion';
    const ESTADO_LISTO           = 'listo';
    const ESTADO_ENTREGADO       = 'entregado';
    const ESTADO_CANCELADO       = 'cancelado';

    const TIPO_DELIVERY = 'delivery';
    const TIPO_RETIRO   = 'retiro';

    public function cliente():BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items():HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function delivery():HasOne
    {
        return $this->hasOne(DetalleDelivery::class);
    }

    public function comanda():HasOne
    {
        return $this->hasOne(Comanda::class);
    }

    public function operador():BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Helpers útiles
    public function esDelivery(): bool
    {
        return $this->tipo === self::TIPO_DELIVERY;
    }

    public function nombreMostrar(): string
    {
        return $this->cliente?->nombre ?? $this->nombre_cliente ?? 'Sin nombre';
    }

    public function telefonoMostrar(): string
    {
        return $this->cliente?->telefono ?? $this->telefono_cliente ?? '-';
    }

    // Generar número correlativo: PED-20250615-001
    public static function generarNumero(): string
    {
        $hoy      = now()->format('Ymd');
        $prefijo  = "PED-{$hoy}-";
        $ultimo   = static::where('numero', 'like', "{$prefijo}%")
                          ->orderByDesc('numero')
                          ->value('numero');

        $correlativo = $ultimo
            ? (int) substr($ultimo, -3) + 1
            : 1;

        return $prefijo . str_pad($correlativo, 3, '0', STR_PAD_LEFT);
    }
}
