<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('subscriber_id')->constrained('subscribers')->onDelete('cascade');
            $table->foreignId('reading_id')->constrained('readings')->onDelete('cascade');
            $table->string('ciclo');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('descuentos', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('saldo', 14, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagada', 'parcial', 'anulada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
