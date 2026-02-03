<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
        'municipio',
        'departamento',
        'representante_legal',
        'logo'
    ];
}
