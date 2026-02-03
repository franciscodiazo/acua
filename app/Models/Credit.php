<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_id',
        'invoice_id',
        'numero',
        'concepto',
        'monto',
        'saldo',
        'tipo',
        'fecha',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo' => 'decimal:2',
        'fecha' => 'date'
    ];

    public static $tipos = [
        'favor' => 'Crédito a Favor',
        'deuda' => 'Deuda/Financiación'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }

    public static function generarNumero($tipo = 'deuda')
    {
        $prefijo = $tipo === 'favor' ? 'CF' : 'CD';
        $anio = date('Y');
        $ultimo = self::where('tipo', $tipo)->whereYear('created_at', $anio)->count() + 1;
        return $prefijo . '-' . $anio . '-' . str_pad($ultimo, 5, '0', STR_PAD_LEFT);
    }

    public function actualizarSaldo()
    {
        $totalAbonado = $this->payments()->where('estado', 'activo')->sum('monto');
        $this->saldo = $this->monto - $totalAbonado;
        
        if ($this->saldo <= 0) {
            $this->saldo = 0;
            $this->estado = 'aplicado';
        } elseif ($totalAbonado > 0) {
            $this->estado = 'activo';
        }
        
        $this->save();
    }
}
