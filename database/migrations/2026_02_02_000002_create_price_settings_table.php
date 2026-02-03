<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('anio');
            $table->decimal('consumo_basico', 10, 2)->default(40); // metros cúbicos básicos
            $table->decimal('cuota_basica', 12, 2)->default(25000); // valor cuota básica
            $table->decimal('tarifa_adicional', 10, 2)->default(1500); // por metro adicional
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_settings');
    }
};
