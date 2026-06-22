<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // ej: PED-20250615-001
            $table->enum('tipo', ['delivery', 'retiro']);
            $table->enum('estado', [
                'pendiente',
                'en_preparacion',
                'listo',
                'entregado',
                'cancelado'
            ])->default('pendiente');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            // Para clientes sin registro
            $table->string('nombre_cliente')->nullable();
            $table->string('telefono_cliente')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia']);
            $table->decimal('monto_pagado', 10, 2)->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('users'); // operador que tomó el pedido
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
