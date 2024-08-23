<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    // List all empleados
    public function index()
    {
        return Empleado::all();
    }

    // Store a new empleado
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'cedula' => 'required|string|unique:empleados,cedula',
            'telefono' => 'required|string',
            'tipo_sangre' => 'required|string',
        ]);

        // Create the empleado
        $empleado = Empleado::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'tipo_sangre' => $request->tipo_sangre,
        ]);

        return response()->json($empleado, 201);
    }

    // Show a specific empleado
    public function show($id)
    {
        $empleado = Empleado::findOrFail($id);
        return response()->json($empleado);
    }

    // Update a specific empleado
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'cedula' => 'required|string|unique:empleados,cedula,' . $id,
            'telefono' => 'required|string',
            'tipo_sangre' => 'required|string',
        ]);

        $empleado = Empleado::findOrFail($id);
        $empleado->update($request->all());

        return response()->json($empleado, 200);
    }

    // Delete a specific empleado
    public function destroy($id)
    {
        Empleado::destroy($id);
        return response()->json(null, 204);
    }
}
