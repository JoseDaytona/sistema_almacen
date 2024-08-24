<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $table = 'tblArticulo';

    protected $fillable = [
        'codigo_barra',
        'descripcion',
    ];
    
    public function colocaciones()
    {
        return $this->hasMany(Colocacion::class);
    }

    public function pedidoDetalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}
