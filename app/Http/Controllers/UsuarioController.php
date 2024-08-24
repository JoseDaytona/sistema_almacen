<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    //Control de Permisos
    public function __construct()
    {
        $this->middleware('PermisoAdmin', ['only' => ['destroy']]);
    }

    //Listado General de Usuario
    public function index(Request $request)
    {
        try {
            // Inicia la consulta con el modelo Articulo
            $query = Usuario::query();

            $query->with('empleado');

            // Filtra por codigo barra
            if ($request->has('usuario')) {
                $query->where('usuario', 'LIKE', '%' . $request->usuario . '%');
            }

            // Filtra por nombre de empleado
            if ($request->has('nombre')) {
                $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
            }

            // Filtra por apellido de empleado
            if ($request->has('apellido')) {
                $query->where('apellido', 'LIKE', '%' . $request->apellido . '%');
            }

            // Filtra por cedula de empleado
            if ($request->has('cedula')) {
                $query->where('cedula', $request->cedula);
            }

            // Filtra por tipo sangre de empleado
            if ($request->has('tipo_sangre')) {
                $query->where('tipo_sangre', $request->tipo_sangre);
            }

            //Pagina Actual, por defecto 10
            $perPage = $request->get('per_page', 10);
            
            // Ejecuta la consulta y obtiene los resultados
            $listado = $query->paginate($perPage);

            return response()->json([
                "estado" => true,
                "data" => $listado->items(),
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

    //Registrar usuario 
    public function store(Request $request)
    {
        try {

            //Validar datos
            $request->validate([
                'empleado_id' => 'required|exists:tblempleado,id',
                'usuario' => 'required|string|unique:tblusuario,usuario',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:admin,invitado',
            ]);

            //Registrar usuario
            $usuario = Usuario::create([
                'empleado_id' => $request->empleado_id,
                'role' => $request->role,
                'usuario' => $request->usuario,
                'password' => Hash::make($request->password),
            ]);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $usuario->load('empleado')
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Consultar un urticulo
    public function show($id)
    {
        try {

            //Consulta de usuario
            $usuario = Usuario::with('empleado')->findOrFail($id)->makeHidden("password");

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $usuario
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Actualizar un usuario
    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'empleado_id' => 'required|exists:tblempleado,id',
                'role' => 'required|string|in:admin,invitado',
                'usuario' => 'required|string|unique:tblusuario,usuario,' . $id,
                'password' => 'nullable|string|min:8',
            ]);

            //Consultar Registrar a actualizar
            $usuario = Usuario::findOrFail($id);

            //Actualizar informaciones suministradas
            if ($request->filled('empleado_id')) {
                $usuario->empleado_id = $request->empleado_id;
            }

            if ($request->filled('usuario')) {
                $usuario->usuario = $request->usuario;
            }

            if ($request->filled('role')) {
                $usuario->role = $request->role;
            }

            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }

            $usuario->save();

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $usuario->load('empleado')->makeHidden("password")
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Eliminar un usuario
    public function destroy($id)
    {
        try {
            //Eliminar Registro
            Usuario::destroy($id);

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
