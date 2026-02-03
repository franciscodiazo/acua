<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'subscriber_id',
        'reading_id',
        'ciclo',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'descuentos',
        'total',
        'saldo',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuentos' => 'decimal:2',
        'total' => 'decimal:2',
        'saldo' => 'decimal:2',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function reading()
    {
        return $this->belongsTo(Reading::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public static function generarNumero()
    {
        $anio = date('Y');
        $ultimo = self::whereYear('created_at', $anio)->max('id') ?? 0;
        $consecutivo = str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
        return "FAC-{$anio}-{$consecutivo}";
    }

    public function actualizarSaldo()
    {
        $pagos = $this->payments()->where('estado', 'activo')->sum('monto');
        $this->saldo = $this->total - $pagos;
        
        if ($this->saldo <= 0) {
            $this->estado = 'pagada';
            $this->saldo = 0;
        } elseif ($pagos > 0) {
            $this->estado = 'parcial';
        }
        
        $this->save();
    }
}
