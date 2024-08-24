<?php

namespace Tests\Unit;

use App\Models\Articulo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Colocacion;
use App\Models\Usuario;

class ColocacionControllerTest extends TestCase
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
    public function test_listado_colocaciones()
    {
        Colocacion::factory()->count(15)->create();

        $response = $this->getJson('/api/colocacion');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => ['articulo', 'articulo_id', 'nombre_articulo', 'precio', 'lugar']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_listado_filtrado_colocaciones()
    {
        Colocacion::factory()->count(10)->create();

        $filtro = [
            'codigo_barra' => "8090008571",
            'descripcion' => "Descripcion",
            'precio' => 20,
            'per_page' => 12
        ];

        $response = $this->getJson('/api/colocacion', $filtro);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' =>  ['articulo', 'articulo_id', 'nombre_articulo', 'precio', 'lugar']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_crear_colocacion()
    {
        $articulo = Articulo::factory()->create();

        $data = [
            'articulo_id' => $articulo->id,
            'nombre_articulo' => 'Test Nombre Articulo',
            'precio' => 100,
            'lugar' => 'Test Lugar',
        ];

        $response = $this->postJson('/api/colocacion', $data);

        $response->assertStatus(201);

        $response->assertJson([
            'estado' => true,
            'data' => $data
        ]);

        $this->assertDatabaseHas('tblcolocacion', $data);
    }

    /** @test */
    public function test_muestra_colocacion()
    {
        $colocacion = Colocacion::factory()->create();

        $response = $this->getJson('/api/colocacion/' . $colocacion->id);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $colocacion->id,
                'articulo_id' => $colocacion->articulo_id,
                'nombre_articulo' => $colocacion->nombre_articulo,
                'precio' => $colocacion->precio,
                'lugar' => $colocacion->lugar
            ]
        ]);
    }

    /** @test */
    public function test_actualizar_colocacion()
    {
        $colocacion = Colocacion::factory()->create();

        $data = [
            'articulo_id' => $colocacion->articulo_id,
            'nombre_articulo' => 'Updated Articulo',
            'precio' => 150,
            'lugar' => 'Updated Lugar',
        ];

        $this->user = Usuario::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->putJson('/api/colocacion/' . $colocacion->id, $data);

        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => [
                'id' => $colocacion->id,
                'articulo_id' => $colocacion->articulo_id,
                'nombre_articulo' => 'Updated Articulo',
                'precio' => 150,
                'lugar' => 'Updated Lugar',
            ]
        ]);

        $this->assertDatabaseHas('tblcolocacion', $data);
    }

    /** @test */
    public function test_no_puede_actualizar_colocacion()
    {
        $colocacion = Colocacion::factory()->create();

        $data = [
            'articulo_id' => $colocacion->articulo_id,
            'nombre_articulo' => 'Updated Articulo',
            'precio' => 150,
            'lugar' => 'Updated Lugar',
        ];

        $this->user = Usuario::factory()->create([
            'role' => 'invitado'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->putJson('/api/colocacion/' . $colocacion->id, $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function test_eliminar_colocacion()
    {
        $colocacion = Colocacion::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/colocacion/' . $colocacion->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tblcolocacion', ['id' => $colocacion->id]);
    }

    /** @test */
    public function test_no_puede_eliminar_colocacion()
    {
        $colocacion = Colocacion::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'invitado'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/colocacion/' . $colocacion->id);

        $response->assertStatus(401);
    }
}
