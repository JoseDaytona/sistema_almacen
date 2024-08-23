<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    //Control de Permisos
    public function __construct()
    {
        $this->middleware('PermisoAdmin', ['only' => ['destroy']]);
    }
    
    //Listado General de Clientes
    public function index(Request $request)
    {
        try {
            // Inicia la consulta con el modelo Articulo
            $query = Cliente::query();

            // Filtra por nombre
            if ($request->has('nombre')) {
                $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
            }

            // Filtra por Telefono
            if ($request->has('telefono')) {
                $query->where('telefono', $request->telefono);
            }

            // Filtra por Tipo Cliente
            if ($request->has('tipo_cliente')) {
                $query->where('tipo_cliente', $request->tipo_cliente);
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

    //Registrar Cliente
    public function store(Request $request)
    {
        try {

            //Validar datos
            $request->validate([
                'nombre' => 'required|string',
                'telefono' => 'required|string',
                'tipo_cliente' => 'required|string', // Adjust validation based on allowed types
            ]);
    
            //Registrar cliente
            $cliente = Cliente::create([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'tipo_cliente' => $request->tipo_cliente,
            ]);
    
            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $cliente
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Consultar un cliente
    public function show($id)
    {
        try {
            //Consulta de cliente
            $cliente = Cliente::findOrFail($id);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $cliente
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Actualizar un cliente
    public function update(Request $request, $id)
    {
        try {

            //Validar datos
            $request->validate([
                'nombre' => 'required|string',
                'telefono' => 'required|string',
                'tipo_cliente' => 'required|string', // Adjust validation based on allowed types
            ]);

            //Consultar Registrar a actualizar
            $cliente = Cliente::findOrFail($id);
            
            //Actualizar informacion
            $cliente->update($request->all());

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $cliente
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Eliminar un cliente
    public function destroy($id)
    {
        try {
            //Eliminar Registro
            Cliente::destroy($id);
            
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
