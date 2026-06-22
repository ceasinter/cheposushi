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
        Schema::create('detalle_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->text('direccion');
            $table->string('referencia')->nullable();
            $table->string('comuna')->nullable();
            $table->foreignId('repartidor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', [
                'sin_asignar',
                'asignado',
                'en_camino',
                'entregado'
            ])->default('sin_asignar');
            $table->timestamp('asignado_at')->nullable();
            $table->timestamp('entregado_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_deliveries');
    }
};
