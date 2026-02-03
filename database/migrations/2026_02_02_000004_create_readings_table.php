<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->onDelete('cascade');
            $table->string('ciclo'); // Ej: 2026-1
            $table->decimal('lectura_anterior', 12, 2)->default(0);
            $table->decimal('lectura_actual', 12, 2)->default(0);
            $table->decimal('consumo', 12, 2)->default(0); // Calculado: lecturaActual - lecturaAnterior
            $table->decimal('valor_total', 14, 2)->default(0); // Calculado: tarifa aplicada
            $table->date('fecha');
            $table->enum('estado', ['pendiente', 'facturado', 'anulado'])->default('pendiente');
            $table->timestamps();

            $table->unique(['subscriber_id', 'ciclo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readings');
    }
};
