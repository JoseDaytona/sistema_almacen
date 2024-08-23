<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    //Control de Permisos
    public function __construct()
    {
        $this->middleware('PermisoAdmin', ['only' => ['destroy']]);
    }
    
    //Listado General de empleado
    public function index(Request $request)
    {
        try {
            // Inicia la consulta con el modelo empleados
            $query = Empleado::query();

            // Filtra por nombre
            if ($request->has('nombre')) {
                $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
            }
            
            // Filtra por apellido
            if ($request->has('apellido')) {
                $query->where('apellido', 'LIKE', '%' . $request->apellido . '%');
            }
            
            // Filtra por cedula
            if ($request->has('cedula')) {
                $query->where('cedula', $request->cedula);
            }

            // Filtra por telefono
            if ($request->has('telefono')) {
                $query->where('telefono', $request->telefono);
            }

            // Filtra por tipo de sangre
            if ($request->has('tipo_sangre')) {
                $query->where('tipo_sangre', $request->tipo_sangre);
            }

            //Pagina Actual, por defecto 10
            $perPage = $request->get('per_page', 10);

            // Ejecuta la consulta y obtiene los resultados
            $listado = $query->paginate($perPage);

            return response()->json([
                "estado" => true,
                "data" => $listado,
                "total" => $listado->total(),
                "per_page" => $listado->perPage(),
                "current_page" => $listado->currentPage(),
                "last_page" => $listado->lastPage()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Registrar empleado
    public function store(Request $request)
    {
        try {
            
            //Validar datos
            $request->validate([
                'nombre' => 'required|string',
                'apellido' => 'required|string',
                'cedula' => 'required|string|unique:empleados,cedula',
                'telefono' => 'required|string',
                'tipo_sangre' => 'required|string', // Adjust validation based on allowed types
            ]);

            //Registrar empleado
            $empleado = Empleado::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'cedula' => $request->cedula,
                'telefono' => $request->telefono,
                'tipo_sangre' => $request->tipo_sangre,
            ]);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $empleado
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Consultar un empleado
    public function show($id)
    {
        try {
            //Consulta de empleado
            $empleado = Empleado::findOrFail($id);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $empleado
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Actualizar un empleado
    public function update(Request $request, $id)
    {
        try {
            //Validar datos
            $request->validate([
                'nombre' => 'required|string',
                'apellido' => 'required|string',
                'cedula' => 'required|string|unique:empleados,cedula,' . $id,
                'telefono' => 'required|string',
                'tipo_sangre' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            ]);
    
            //Consultar Registrar a actualizar
            $empleado = Empleado::findOrFail($id);
    
            //Actualizar informacion
            $empleado->update($request->all());
    
            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $empleado
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Eliminar un empleado
    public function destroy($id)
    {
        try {
            //Eliminar Registro
            Empleado::destroy($id);

            //Retorno de respuesta satisfactoria
            return response()->json(null, 204);

        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }
}
