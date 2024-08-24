<?php

namespace Tests\Unit;

use App\Models\Empleado;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioControllerTest extends TestCase
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
    public function test_listado_usuarios()
    {
        Usuario::factory()->count(15)->create();

        $response = $this->getJson('/api/usuario');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => ["usuario", "role"]],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_listado_filtrado_usuarios()
    {
        Usuario::factory()->count(10)->create();

        $filtro = [
            'nombre' => "Jose Miguel",
            'usuario' => "jose.miguel",
            'per_page' => 20
        ];

        $response = $this->getJson('/api/usuario', $filtro);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' =>  ["usuario", "role"]],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_crear_usuario()
    {
        $empleado = Empleado::factory()->create();

        $data = [
            'empleado_id' => $empleado->id,
            'role' => "admin",
            'usuario' => "usuario.prueba",
            'password' => "123456789",
        ];

        $response = $this->postJson('/api/usuario', $data);

        $response->assertStatus(201);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'empleado_id' => $empleado->id,
                'role' => "admin",
                'usuario' => "usuario.prueba",
                'empleado' => [
                    "nombre" =>  $empleado->nombre,
                    "apellido" =>  $empleado->apellido,
                    "cedula" =>  $empleado->cedula,
                    "telefono" =>  $empleado->telefono,
                    "tipo_sangre" => $empleado->tipo_sangre,
                    "id" =>  $empleado->id
                ]
            ]
        ]);
    }

    /** @test */
    public function test_muestra_usuario()
    {
        $usuario = Usuario::factory()->create();

        $response = $this->getJson('/api/usuario/' . $usuario->id);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $usuario->id,
                'empleado_id' => $usuario->empleado_id,
                'role' => $usuario->role,
                'usuario' => $usuario->usuario
            ]
        ]);
    }

    /** @test */
    public function test_actualizar_usuario()
    {
        $empleado = Empleado::factory()->create();

        $usuario = Usuario::factory()->create();

        $data = [
            'empleado_id' => $empleado->id,
            'role' => "invitado",
            'usuario' => "usuario.probador",
            'password' => "123456789",
        ];

        $response = $this->putJson('/api/usuario/' . $usuario->id, $data);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $usuario->id,
                'empleado_id' => $empleado->id,
                'role' => "invitado",
                'usuario' => "usuario.probador",
                'empleado' => [
                    "id" =>  $empleado->id,
                    "nombre" =>  $empleado->nombre,
                    "apellido" =>  $empleado->apellido,
                    "cedula" =>  $empleado->cedula,
                    "telefono" =>  $empleado->telefono,
                    "tipo_sangre" => $empleado->tipo_sangre
                ]
            ]
        ]);
    }

    /** @test */
    public function test_eliminar_usuario()
    {
        $usuario = Usuario::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/usuario/' . $usuario->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tblusuario', ['id' => $usuario->id]);
    }

    /** @test */
    public function test_no_puede_eliminar_usuario()
    {
        $usuario = Usuario::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'invitado'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/usuario/' . $usuario->id);

        $response->assertStatus(401);
    }
}
