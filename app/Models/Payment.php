<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'subscriber_id',
        'numero_recibo',
        'monto',
        'fecha',
        'metodo_pago',
        'estado',
        'observaciones'
    ];

    public static $metodosPago = [
        'efectivo' => 'Efectivo',
        'banco' => 'Consignación Banco',
        'transferencia' => 'Transferencia',
        'cheque' => 'Cheque',
        'otro' => 'Otro'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public static function generarNumeroRecibo()
    {
        $anio = date('Y');
        $ultimo = self::whereYear('created_at', $anio)->max('id') ?? 0;
        $consecutivo = str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
        return "REC-{$anio}-{$consecutivo}";
    }
}
