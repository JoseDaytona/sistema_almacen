<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'tblCliente';
    
    protected $fillable = [
        'nombre',
        'telefono',
        'tipo_cliente',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
