<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'anio',
        'consumo_basico',
        'cuota_basica',
        'tarifa_adicional',
        'activo'
    ];

    protected $casts = [
        'consumo_basico' => 'decimal:2',
        'cuota_basica' => 'decimal:2',
        'tarifa_adicional' => 'decimal:2',
        'activo' => 'boolean'
    ];

    public static function getActiveForYear($year = null)
    {
        $year = $year ?? date('Y');
        
        return self::where('anio', $year)
            ->where('activo', true)
            ->first() ?? self::where('activo', true)->orderBy('anio', 'desc')->first();
    }

    public function calcularValor($consumo)
    {
        if ($consumo <= $this->consumo_basico) {
            return $this->cuota_basica;
        }

        $metrosAdicionales = $consumo - $this->consumo_basico;
        return $this->cuota_basica + ($metrosAdicionales * $this->tarifa_adicional);
    }
}
