<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colocacion;

class ColocacionController extends Controller
{
    // List all colocaciones
    public function index()
    {
        return Colocacion::with('articulo')->get();
    }

    // Store a new colocacion
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'articulo_id' => 'required|exists:tblArticulo,id',
            'nombre_articulo' => 'required|string|unique:tblColocacion,nombre_articulo,id,precio,' . $request->precio,
            'precio' => 'required|numeric',
            'lugar' => 'required|string',
        ]);

        // Create the colocacion
        $colocacion = Colocacion::create([
            'articulo_id' => $request->articulo_id,
            'nombre_articulo' => $request->nombre_articulo,
            'precio' => $request->precio,
            'lugar' => $request->lugar,
        ]);

        return response()->json($colocacion->load('articulo'), 201);
    }

    // Show a specific colocacion with its articulo
    public function show($id)
    {
        $colocacion = Colocacion::with('articulo')->findOrFail($id);
        return response()->json($colocacion);
    }

    // Update a specific colocacion
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'articulo_id' => 'required|exists:articulos,id',
            'nombre_articulo' => 'required|string|unique:colocaciones,nombre_articulo,' . $id . ',id,precio,' . $request->precio,
            'precio' => 'required|numeric',
            'lugar' => 'required|string',
        ]);

        $colocacion = Colocacion::findOrFail($id);
        $colocacion->update($request->all());

        return response()->json($colocacion->load('articulo'), 200);
    }

    // Delete a specific colocacion
    public function destroy($id)
    {
        Colocacion::destroy($id);
        return response()->json(null, 204);
    }
}
