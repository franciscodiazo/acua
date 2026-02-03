<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_id',
        'ciclo',
        'lectura_anterior',
        'lectura_actual',
        'consumo',
        'valor_total',
        'fecha',
        'estado'
    ];

    protected $casts = [
        'lectura_anterior' => 'decimal:2',
        'lectura_actual' => 'decimal:2',
        'consumo' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'fecha' => 'date'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function calcularConsumo()
    {
        $this->consumo = $this->lectura_actual - $this->lectura_anterior;
        return $this->consumo;
    }

    public function calcularValorTotal()
    {
        $anio = substr($this->ciclo, 0, 4);
        $config = PriceSetting::getActiveForYear($anio);
        
        if ($config) {
            $this->valor_total = $config->calcularValor($this->consumo);
        }
        
        return $this->valor_total;
    }

    public static function getCiclosDisponibles()
    {
        $anioActual = date('Y');
        $ciclos = [];
        
        for ($i = 1; $i <= 6; $i++) {
            $ciclos[] = "{$anioActual}-{$i}";
        }
        
        return $ciclos;
    }

    public static function getLecturaAnterior($subscriberId, $cicloActual = null)
    {
        $query = self::where('subscriber_id', $subscriberId)
            ->where('estado', '!=', 'anulado')
            ->orderBy('ciclo', 'desc');
        
        if ($cicloActual) {
            $query->where('ciclo', '<', $cicloActual);
        }
        
        $ultima = $query->first();
        
        return $ultima ? $ultima->lectura_actual : 0;
    }
}
