<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('subscribers')->onDelete('cascade');
            $table->string('numero_recibo')->unique();
            $table->decimal('monto', 14, 2);
            $table->date('fecha');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'otro'])->default('efectivo');
            $table->enum('estado', ['activo', 'anulado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
