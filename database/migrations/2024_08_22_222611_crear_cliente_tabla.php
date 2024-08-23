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
        Schema::create('tblCliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono')->unique();
            $table->enum('tipo_cliente', ['Fisica', 'Juridico']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblCliente');
    }
};
