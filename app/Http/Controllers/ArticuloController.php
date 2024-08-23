<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articulo;
/**
 * @OA\Info(title="My First API", version="0.1")
 */
class ArticuloController extends Controller
{
    public function index()
    {
        return Articulo::all();
    }

    // Store a new articulo
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'codigo_barra' => 'required|string|unique:articulos,codigo_barra',
            'descripcion' => 'required|string',
        ]);

        // Create the articulo
        $articulo = Articulo::create([
            'codigo_barra' => $request->codigo_barra,
            'descripcion' => $request->descripcion,
        ]);

        return response()->json($articulo, 201);
    }

    // Show a specific articulo
    public function show($id)
    {
        $articulo = Articulo::findOrFail($id);
        return response()->json($articulo);
    }

    // Update a specific articulo
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'codigo_barra' => 'required|string|unique:articulos,codigo_barra,' . $id,
            'descripcion' => 'required|string',
        ]);

        $articulo = Articulo::findOrFail($id);
        $articulo->update($request->all());

        return response()->json($articulo, 200);
    }

    // Delete a specific articulo
    public function destroy($id)
    {
        Articulo::destroy($id);
        return response()->json(null, 204);
    }
}
