<?php

namespace Tests\Unit;

use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\Colocacion;
use App\Models\PedidoDetalle;
use App\Models\Usuario;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoControllerTest extends TestCase
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
    public function test_listado_pedidos()
    {
        PedidoDetalle::factory()->count(15)->create();

        $response = $this->getJson('/api/pedido');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => [
                'cliente_id',
                'cliente',
                'pedido_detalles'
            ]],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_listado_filtrado_pedidos()
    {
        PedidoDetalle::factory()->count(10)->create();

        $filtro = [
            'nombre_cliente' => "Jose Miguel",
            'nombre_cliente' => "1212132332",
            'per_page' => 20
        ];

        $response = $this->getJson('/api/pedido', $filtro);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' =>  [
                'cliente_id',
                'cliente',
                'pedido_detalles'
            ]],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_crear_pedido()
    {
        $cliente = Cliente::factory()->create();
        $articulo = Articulo::factory()->create();
        $colocacion = Colocacion::factory()->create();

        $data = [
            'cliente_id' => $cliente->id,
            'detalles' => [
                [
                    'articulo_id' => $articulo->id,
                    'colocacion_id' => $colocacion->id,
                    'cantidad' => 12
                ]
            ]
        ];

        $response = $this->postJson('/api/pedido', $data);

        $response->assertStatus(201);

        $response->assertJson([
            'estado' => true,
            //'data' => $data
        ]);

        //$this->assertDatabaseHas('tblpedido', $data);
    }

    /** @test */
    public function test_muestra_pedido()
    {
        $pedido = PedidoDetalle::factory()->create();

        $response = $this->getJson('/api/pedido/' . $pedido->pedido_id);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
        ]);
    }

    /** @test */
    public function test_actualizar_pedido()
    {
        $pedido = PedidoDetalle::factory()->create();

        $cliente = Cliente::factory()->create();
        $articulo = Articulo::factory()->create();
        $colocacion = Colocacion::factory()->create();

        $data = [
            'id' => $pedido->pedido_id,
            'cliente_id' => $cliente->id,
            'detalles' => [
                [
                    'pedido_id' => $pedido->pedido_id,
                    'articulo_id' => $articulo->id,
                    'colocacion_id' => $colocacion->id,
                    'cantidad' => 12
                ]
            ]
        ];

        $response = $this->putJson('/api/pedido/' . $pedido->pedido_id, $data);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            //'data' => $data
        ]);
    }

    /** @test */
    public function test_eliminar_pedido()
    {
        $pedido = PedidoDetalle::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/pedido/' . $pedido->pedido_id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tblpedido', ['id' => $pedido->pedido_id]);
    }

    /** @test */
    public function test_no_puede_eliminar_pedido()
    {
        $pedido = PedidoDetalle::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'invitado'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/pedido/' . $pedido->pedido_id);

        $response->assertStatus(401);
    }
}
