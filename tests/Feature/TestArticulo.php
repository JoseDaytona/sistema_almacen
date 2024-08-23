<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TestArticulo extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(Usuario::class)->create();

        $this->actingAs($this->user, "api");

    }

    public function test_create_articulo(): void
    {
        $formulario = [
            'codigo_barra' => "00011",
            'descripcion' => "Descripcion Articulo"
        ];

        $this->json("POST", route("articulo.store"), $formulario)
                ->assertStatus(201)
                ->assertJson([ "data" => $formulario ]);
    }
}
