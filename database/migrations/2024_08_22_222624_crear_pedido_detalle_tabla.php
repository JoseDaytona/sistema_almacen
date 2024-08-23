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
        Schema::create('tblPedidoDetalle', function (Blueprint $table) {
            $table->foreignId('pedido_id')->constrained('tblPedido')->onDelete('cascade');
            $table->foreignId('articulo_id')->constrained('tblArticulo')->onDelete('cascade');
            $table->foreignId('colocacion_id')->constrained('tblColocacion')->onDelete('cascade');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblPedidoDetalle');
    }
};
