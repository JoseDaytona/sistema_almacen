<?php

namespace Database\Factories;

use App\Models\Articulo;
use App\Models\Colocacion;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoDetalleFactory extends Factory
{
    protected $model = PedidoDetalle::class;

    public function definition()
    {
        return [
            'pedido_id' => Pedido::factory(),
            'articulo_id' => Articulo::factory(),
            'colocacion_id' => Colocacion::factory(),
            'cantidad' => $this->faker->numberBetween(1, 10000000)
        ];
    }
}
