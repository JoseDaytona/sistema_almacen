<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cliente;
use App\Models\Usuario;

class ClienteControllerTest extends TestCase
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
    public function test_listado_clientes()
    {
        Cliente::factory()->count(15)->create();

        $response = $this->getJson('/api/cliente');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => ['nombre', 'telefono', 'tipo_cliente']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_listado_filtrado_clientes()
    {
        Cliente::factory()->count(10)->create();

        $filtro = [
            'nombre' => "Nombre 2",
            'telefono' => "8090008571",
            'tipo_cliente' => "Juridico",
            'per_page' => 20
        ];

        $response = $this->getJson('/api/cliente', $filtro);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => ['id', 'nombre', 'telefono', 'tipo_cliente']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_crear_cliente()
    {
        $data = [
            'nombre' => "Nombre 1",
            'telefono' => "8090008571",
            'tipo_cliente' => "Fisica",
        ];

        $response = $this->postJson('/api/cliente', $data);

        $response->assertStatus(201);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'nombre' => "Nombre 1",
                'telefono' => "8090008571",
                'tipo_cliente' => "Fisica",
            ]
        ]);

        $this->assertDatabaseHas('tblcliente', $data);
    }

    /** @test */
    public function test_muestra_cliente()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->getJson('/api/cliente/' . $cliente->id);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'telefono' => $cliente->telefono,
                'tipo_cliente' => $cliente->tipo_cliente
            ]
        ]);
    }

    /** @test */
    public function test_actualiza_cliente()
    {
        $cliente = Cliente::factory()->create();

        $data = [
            'nombre' => "Nombre Actualizado",
            'telefono' => "809000000",
            'tipo_cliente' => "Juridico",
        ];

        $response = $this->putJson('/api/cliente/' . $cliente->id, $data);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $cliente->id,
                'nombre' => "Nombre Actualizado",
                'telefono' => "809000000",
                'tipo_cliente' => "Juridico",
            ]
        ]);

        $this->assertDatabaseHas('tblcliente', $data);
    }

    /** @test */
    public function test_eliminar_cliente()
    {
        $cliente = Cliente::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/cliente/' . $cliente->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tblcliente', ['id' => $cliente->id]);
    }

    /** @test */
    public function test_no_puede_eliminar_cliente()
    {
        $cliente = Cliente::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'invitado'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/cliente/' . $cliente->id);

        $response->assertStatus(401);
    }
}
