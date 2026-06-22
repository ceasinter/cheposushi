<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cliente extends Model
{
    protected $fillable = [
        'nombre', 'telefono', 'email', 'direccion_principal'
    ];

    public function pedidos():HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function ultimoPedido():HasOne
    {
        return $this->hasOne(Pedido::class)->latestOfMany();
    }
}
