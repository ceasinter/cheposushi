<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'descripcion',
        'precio', 'imagen', 'disponible', 'orden'
    ];

    protected $casts = [
        'precio'     => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public function categoria():BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function pedidoItems():HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
