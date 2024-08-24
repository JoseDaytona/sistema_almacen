<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colocacion extends Model
{
    use HasFactory;
    
    protected $table = 'tblColocacion';
    
    protected $fillable = [
        'articulo_id',
        'nombre_articulo',
        'precio',
        'lugar',
    ];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function pedidoDetalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}
