<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colocacion;

class ColocacionController extends Controller
{
     //Control de Permisos
     public function __construct()
     {
        $this->middleware('PermisoAdmin', ['only' => ['update']]);
        $this->middleware('PermisoAdmin', ['only' => ['destroy']]);
     }
     
    //Listado General de Colocaciones
    public function index(Request $request)
    {
        try {
            // Inicia la consulta con el modelo Articulo
            $query = Colocacion::query();

            //Join con tabla Articulos Basado en su clave foranea
            $query->with('articulo');

            // Filtra por codigo barra
            if ($request->has('codigo_barra')) {
                $query->where('codigo_barra', 'LIKE', '%' . $request->codigo_barra . '%');
            }
            
            // Filtra por Descripcion Articulo
            if ($request->has('descripcion_articulo')) {
                $query->where('descripcion', 'LIKE', '%' . $request->descripcion_articulo . '%');
            }

            // Filtra por nombre
            if ($request->has('nombre_articulo')) {
                $query->where('nombre_articulo', 'LIKE', '%' . $request->nombre_articulo . '%');
            }

            // Filtra por nombre
            if ($request->has('lugar')) {
                $query->where('lugar', 'LIKE', '%' . $request->lugar . '%');
            }

            //Filtar por Precio en especifico
            if ($request->has('precio')) {
                $query->where('precio', '=', $request->precio);
            }

            // Filtra por rango de precio
            if ($request->has('precio_min')) {
                $query->where('precio', '>=', $request->precio_min);
            }

            // Filtra por rango de precio
            if ($request->has('precio_max')) {
                $query->where('precio', '<=', $request->precio_max);
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

    //Registrar colocacion
    public function store(Request $request)
    {
        try {

            //Validar datos
            $request->validate([
                'articulo_id' => 'required|exists:tblArticulo,id',
                'nombre_articulo' => 'required|string|unique:tblColocacion,nombre_articulo,id,precio,' . $request->precio,
                'precio' => 'required|numeric',
                'lugar' => 'required|string',
            ]);

            //Registrar colocacion
            $colocacion = Colocacion::create([
                'articulo_id' => $request->articulo_id,
                'nombre_articulo' => $request->nombre_articulo,
                'precio' => $request->precio,
                'lugar' => $request->lugar,
            ]);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $colocacion->load('articulo')
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Consultar una colocacion
    public function show($id)
    {
        try {
            //Consulta de colocacion
            $colocacion = Colocacion::with('articulo')->findOrFail($id);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $colocacion
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Actualizar una colocacion
    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'articulo_id' => 'required|exists:articulos,id',
                'nombre_articulo' => 'required|string|unique:colocaciones, nombre_articulo,' . $id . ', id, precio,' . $request->precio,
                'precio' => 'required|numeric',
                'lugar' => 'required|string',
            ]);
    
            //Consultar Registrar a colocacion
            $colocacion = Colocacion::findOrFail($id);
    
            //Actualizar informacion
            $colocacion->update($request->all());
    
            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $colocacion->load('articulo')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    //Eliminar una colocacion
    public function destroy($id)
    {
        try {
            //Eliminar Registro
            Colocacion::destroy($id);
            
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
