<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articulo;

class ArticuloController extends Controller
{
    //Control de Permisos
    public function __construct()
    {
        $this->middleware('PermisoAdmin', ['only' => ['destroy']]);
    }

    //Listado General de Articulos
    public function index(Request $request)
    {
        try {
            // Inicia la consulta con el modelo Articulo
            $query = Articulo::query();

            // Filtra por codigo barra
            if ($request->has('codigo_barra')) {
                $query->where('codigo_barra', 'LIKE', '%' . $request->codigo_barra . '%');
            }

            // Filtra por Descripcion
            if ($request->has('descripcion')) {
                $query->where('descripcion', 'LIKE', '%' . $request->descripcion . '%');
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

    //Registrar articulo
    public function store(Request $request)
    {
        try {

            //Validar datos
            $request->validate([
                'codigo_barra' => 'required|string|unique:tblarticulo,codigo_barra',
                'descripcion' => 'required|string',
            ]);

            //Registrar articulo
            $articulo = Articulo::create([
                'codigo_barra' => $request->codigo_barra,
                'descripcion' => $request->descripcion,
            ]);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $articulo
            ], 201);
            
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Consultar un articulo
    public function show($id)
    {
        try {
            //Consulta de articulo
            $articulo = Articulo::findOrFail($id);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $articulo
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Actualizar un articulo
    public function update(Request $request, $id)
    {
        try {
            //Validar datos
            $request->validate([
                'codigo_barra' => 'required|string|unique:tblarticulo,codigo_barra,' . $id,
                'descripcion' => 'required|string',
            ]);

            //Consultar Registrar a actualizar
            $articulo = Articulo::findOrFail($id);

            //Actualizar informacion
            $articulo->update($request->all());

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $articulo
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Eliminar un articulo
    public function destroy($id)
    {
        try {
            //Eliminar Registro
            Articulo::destroy($id);

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
