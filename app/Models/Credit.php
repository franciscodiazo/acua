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
        'concepto',
        'monto',
        'fecha',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
