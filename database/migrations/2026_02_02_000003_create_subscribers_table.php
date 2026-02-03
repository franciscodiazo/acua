<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->string('documento');
            $table->string('apellidos');
            $table->string('nombres');
            $table->string('correo')->nullable();
            $table->integer('estrato')->default(1);
            $table->string('telefono')->nullable();
            $table->string('sector')->nullable();
            $table->integer('no_personas')->default(1);
            $table->string('direccion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
