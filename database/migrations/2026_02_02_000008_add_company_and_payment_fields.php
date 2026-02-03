<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campos a companies
        Schema::table('companies', function (Blueprint $table) {
            $table->string('cuenta_bancaria')->nullable()->after('logo');
            $table->string('banco')->nullable()->after('cuenta_bancaria');
            $table->text('mensaje_factura')->nullable()->after('banco');
        });

        // Agregar el nuevo campo metodo_pago con más opciones (string en lugar de enum restrictivo)
        // Primero eliminar la columna vieja y crear una nueva
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('metodo_pago')->default('efectivo')->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['cuenta_bancaria', 'banco', 'mensaje_factura']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'otro'])->default('efectivo')->after('fecha');
        });
    }
};
