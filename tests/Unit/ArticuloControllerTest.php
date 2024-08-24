<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Articulo;
use App\Models\Usuario;

class ArticuloControllerTest extends TestCase
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
    public function test_listado_articulos()
    {
        Articulo::factory()->count(15)->create();
        
        $response = $this->getJson('/api/articulo');
        
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => ['id', 'codigo_barra', 'descripcion']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }
    
    /** @test */
    public function test_listado_filtrado_articulos()
    {
        Articulo::factory()->count(10)->create();
        
        $filtro = [
            'codigo_barra' => '123456789012',
            'descripcion' => 'Test Descripcion',
            'per_page' => 20
        ];

        $response = $this->getJson('/api/articulo', $filtro);
        
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'estado',
            'data' => ['*' => ['id', 'codigo_barra', 'descripcion']],
            'total',
            'per_page',
            'current_page',
            'last_page'
        ]);
    }

    /** @test */
    public function test_crear_articulo()
    {
        $data = [
            'codigo_barra' => '123456789012',
            'descripcion' => 'Test Descripcion',
        ];

        $response = $this->postJson('/api/articulo', $data);
        
        $response->assertStatus(201);

        $response->assertJson([
            'estado' => true,
            'data' => ['codigo_barra' => '123456789012', 'descripcion' => 'Test Descripcion']
        ]);

        $this->assertDatabaseHas('tblarticulo', $data);
    }

    /** @test */
    public function test_muestra_articulo()
    {
        $articulo = Articulo::factory()->create();
        
        $response = $this->getJson('/api/articulo/' . $articulo->id);
        
        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => ['id' => $articulo->id, 'codigo_barra' => $articulo->codigo_barra, 'descripcion' => $articulo->descripcion]
        ]);
    }

    /** @test */
    public function test_actualiza_articulo()
    {
        $articulo = Articulo::factory()->create();

        $data = [
            'codigo_barra' => '987654321098',
            'descripcion' => 'Actualizar Descripcion'
        ];
        
        $response = $this->putJson('/api/articulo/' . $articulo->id, $data);
        
        $response->assertStatus(200);

        $response->assertJson([
            'estado' => true,
            'data' => ['id' => $articulo->id, 'codigo_barra' => '987654321098', 'descripcion' => 'Actualizar Descripcion']
        ]);

        $this->assertDatabaseHas('tblarticulo', $data);
    }

    /** @test */
    public function test_eliminar_articulo()
    {
        $articulo = Articulo::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user, "api");
        
        $response = $this->deleteJson('/api/articulo/' . $articulo->id);
        
        $response->assertStatus(204);

        $this->assertDatabaseMissing('tblarticulo', ['id' => $articulo->id]);
    }

    /** @test */
    public function test_no_puede_eliminar_articulo()
    {
        $articulo = Articulo::factory()->create();

        $this->user = Usuario::factory()->create([
            'role' => 'invitado'
        ]);

        $this->actingAs($this->user, "api");

        $response = $this->deleteJson('/api/articulo/' . $articulo->id);

        $response->assertStatus(401);
    }
}
