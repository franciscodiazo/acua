<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricula',
        'documento',
        'apellidos',
        'nombres',
        'correo',
        'estrato',
        'telefono',
        'sector',
        'no_personas',
        'direccion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function readings()
    {
        return $this->hasMany(Reading::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function getUltimaLecturaAttribute()
    {
        return $this->readings()->orderBy('ciclo', 'desc')->first();
    }
}
