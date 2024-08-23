<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $table = 'tblPedidoDetalle';
    
    protected $fillable = [
        'pedido_id',
        'articulo_id',
        'colocacion_id',
        'cantidad',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function colocacion()
    {
        return $this->belongsTo(Colocacion::class);
    }
}
