<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
        return Cliente::all();
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'nombre' => 'required|string',
            'telefono' => 'required|string',
            'tipo_cliente' => 'required|string', // Adjust validation based on allowed types
        ]);

        // Create the cliente
        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'tipo_cliente' => $request->tipo_cliente,
        ]);

        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'nombre' => 'required|string',
            'telefono' => 'required|string',
            'tipo_cliente' => 'required|string', // Adjust validation based on allowed types
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());

        return response()->json($cliente, 200);
    }

    public function destroy($id)
    {
        Cliente::destroy($id);
        return response()->json(null, 204);
    }
}
