<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar saldo a credits para trackear abonos
        Schema::table('credits', function (Blueprint $table) {
            $table->decimal('saldo', 14, 2)->default(0)->after('monto');
            $table->string('tipo')->default('favor')->after('saldo'); // 'favor' = a favor del cliente, 'deuda' = deuda del cliente
            $table->string('numero')->nullable()->after('id');
        });

        // Actualizar saldo inicial igual al monto
        \DB::statement("UPDATE credits SET saldo = monto");

        // Crear tabla para abonos a créditos (deudas)
        Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->constrained('credits')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('subscribers')->onDelete('cascade');
            $table->string('numero_recibo')->unique();
            $table->decimal('monto', 14, 2);
            $table->date('fecha');
            $table->string('metodo_pago')->default('efectivo');
            $table->enum('estado', ['activo', 'anulado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_payments');
        
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn(['saldo', 'tipo', 'numero']);
        });
    }
};
