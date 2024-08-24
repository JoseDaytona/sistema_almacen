<?php

namespace Database\Factories;

use App\Models\Empleado;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        return [
            'empleado_id' => Empleado::factory(),
            'role' => $this->faker->randomElement(['admin', 'invitado']),
            'usuario' => $this->faker->unique()->userName,
            'password' => Hash::make('password'),
        ];
    }
}
