<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
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
