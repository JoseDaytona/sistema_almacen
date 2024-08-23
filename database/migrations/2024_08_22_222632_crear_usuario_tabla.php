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
        Schema::create('tblUsuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('tblEmpleado')->onDelete('cascade');
            $table->enum('role', ['admin', 'invitado']);
            $table->string('usuario')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblUsuario');
    }
};
