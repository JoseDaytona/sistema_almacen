<?php

namespace Tests\Unit;

use App\Models\Empleado;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;

class EmpleadoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Usuario::factory()->create();

        $this->actingAs($this->user, "api");
    }

    /** @test */
    public function test_listado_empleados()
    {
        Empleado::factory()->count(15)->create();

        $response = $this->getJson('/api/empleado');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => ['nombre', 'apellido', 'cedula','telefono', 'tipo_sangre']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_listado_filtrado_empleados()
    {
        Empleado::factory()->count(10)->create();

        $filtro = [
            'nombre' => "Jose Miguel",
            'tipo_sangre' => "B-",
            'per_page' => 20
        ];

        $response = $this->getJson('/api/empleado', $filtro);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' =>  ['nombre', 'apellido', 'cedula','telefono', 'tipo_sangre']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_crear_empleado()
    {
        $data = [
            'nombre' => "Luis Miguel",
            'apellido' => "Reyes Rosario",
            'cedula' => "40230000000",
            'telefono' => "8090000000",
            'tipo_sangre' => "B+"
        ];

        $response = $this->postJson('/api/empleado', $data);

        $response->assertStatus(201);

        $response->assertJson([
            'estado' => true,
            'data' => $data
        ]);

        $this->assertDatabaseHas('tblempleado', $data);
    }

    /** @test */
    public function test_muestra_empleado()
    {
        $empleado = Empleado::factory()->create();

        $response = $this->getJson('/api/empleado/' . $empleado->id);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $empleado->id,
                'nombre' =>  $empleado->nombre,
                'apellido' =>  $empleado->apellido,
                'cedula' =>  $empleado->cedula,
                'telefono' =>  $empleado->telefono,
                'tipo_sangre' =>  $empleado->tipo_sangre
            ]
        ]);
    }

    /** @test */
    public function test_actualizar_empleado()
    {
        $empleado = Empleado::factory()->create();

        $data = [
            'nombre' => "Henry Miguel",
            'apellido' => "Reyes Rosario",
            'cedula' => "40230000000",
            'telefono' => "8090000000",
            'tipo_sangre' => "B+"
        ];

        $response = $this->putJson('/api/empleado/' . $empleado->id, $data);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $empleado->id,
                'nombre' => "Henry Miguel",
                'apellido' => "Reyes Rosario",
                'cedula' => "40230000000",
                'telefono' => "8090000000",
                'tipo_sangre' => "B+"
            ]
        ]);

        $this->assertDatabaseHas('tblempleado', $data);
    }

    /** @test */
    public function test_eliminar_empleado()
    {
        $empleado = Empleado::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/empleado/' . $empleado->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tblempleado', ['id' => $empleado->id]);
    }

    /** @test */
    public function test_no_puede_eliminar_empleado()
    {
        $empleado = Empleado::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'invitado'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/empleado/' . $empleado->id);

        $response->assertStatus(401);
    }
}
