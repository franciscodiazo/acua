<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\PriceSetting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear empresa por defecto
        Company::create([
            'nombre' => 'Acueducto Rural Comunitario',
            'nit' => '900.123.456-7',
            'direccion' => 'Calle Principal # 1-23',
            'telefono' => '310 123 4567',
            'email' => 'contacto@acueducto.com',
            'municipio' => 'Mi Municipio',
            'departamento' => 'Mi Departamento',
            'representante_legal' => 'Juan Pérez García'
        ]);

        // Crear configuración de precios para 2026
        PriceSetting::create([
            'anio' => 2026,
            'consumo_basico' => 40,
            'cuota_basica' => 25000,
            'tarifa_adicional' => 1500,
            'activo' => true
        ]);
    }
}
