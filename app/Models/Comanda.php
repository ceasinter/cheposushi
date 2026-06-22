<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comanda extends Model
{
    protected $fillable = [
        'pedido_id', 'numero_comanda', 'impresa_at'
    ];

    protected $casts = [
        'impresa_at' => 'datetime',
    ];

    public function pedido():BelongsTo
    {
        return $this->belongsTo(Pedido::class)->with(['items.producto', 'delivery']);
    }

    public function marcarImpresa(): void
    {
        $this->update(['impresa_at' => now()]);
    }

    public function yaFueImpresa(): bool
    {
        return !is_null($this->impresa_at);
    }

    // Número correlativo diario para la cocina (ej: #7)
    public static function siguienteNumero(): int
    {
        return static::whereDate('created_at', today())->max('numero_comanda') + 1;
    }
}
