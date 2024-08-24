<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;
    
    protected $table = 'tblEmpleado';
    
    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'telefono',
        'tipo_sangre',
    ];

    public function usuario()
    {
        return $this->hasOne(Usuario::class);
    }
}
