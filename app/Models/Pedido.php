<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    
    protected $table = 'tblPedido';
    
    protected $fillable = [
        'cliente_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pedidoDetalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}
