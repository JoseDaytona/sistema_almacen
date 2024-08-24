<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tblUsuario';
    
    protected $fillable = [
        'empleado_id',
        'role',
        'usuario',
        'password',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
