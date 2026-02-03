<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_id',
        'subscriber_id',
        'numero_recibo',
        'monto',
        'fecha',
        'metodo_pago',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date'
    ];

    public static $metodosPago = [
        'efectivo' => 'Efectivo',
        'banco' => 'Consignación Banco',
        'transferencia' => 'Transferencia',
        'cheque' => 'Cheque',
        'otro' => 'Otro'
    ];

    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public static function generarNumeroRecibo()
    {
        $anio = date('Y');
        $ultimo = self::whereYear('created_at', $anio)->count() + 1;
        return 'RC-' . $anio . '-' . str_pad($ultimo, 5, '0', STR_PAD_LEFT);
    }
}
