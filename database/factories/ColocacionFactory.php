<?php

namespace Database\Factories;

use App\Models\Articulo;
use App\Models\Colocacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColocacionFactory extends Factory
{
    protected $model = Colocacion::class;

    public function definition()
    {
        return [
            'articulo_id' => Articulo::factory(), 
            'nombre_articulo' => $this->faker->word,
            'precio' => $this->faker->randomFloat(2, 10, 1000),
            'lugar' => $this->faker->city,
        ];
    }
}
