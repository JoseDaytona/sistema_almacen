<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // List all usuarios
    public function index()
    {
        return Usuario::with('empleado')->get();
    }

    // Store a new usuario
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'usuario' => 'required|string|unique:usuarios,usuario',
            'password' => 'required|string|min:8',
        ]);

        // Create the usuario
        $usuario = Usuario::create([
            'empleado_id' => $request->empleado_id,
            'usuario' => $request->usuario,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($usuario->load('empleado'), 201);
    }

    // Show a specific usuario
    public function show($id)
    {
        $usuario = Usuario::with('empleado')->findOrFail($id);
        return response()->json($usuario);
    }

    // Update a specific usuario
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'usuario' => 'required|string|unique:usuarios,usuario,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $usuario = Usuario::findOrFail($id);

        // Update the usuario data
        $usuario->empleado_id = $request->empleado_id;
        $usuario->usuario = $request->usuario;
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        return response()->json($usuario->load('empleado'), 200);
    }

    // Delete a specific usuario
    public function destroy($id)
    {
        Usuario::destroy($id);
        return response()->json(null, 204);
    }
}
