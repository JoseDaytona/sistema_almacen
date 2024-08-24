<?php

namespace Database\Factories;

use App\Models\Articulo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticuloFactory extends Factory
{
    protected $model = Articulo::class;

    public function definition()
    {
        return [
            'codigo_barra' => $this->faker->unique()->ean13(),
            'descripcion' => $this->faker->sentence(),
        ];
    }
}
