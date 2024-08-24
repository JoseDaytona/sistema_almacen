<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoDetalle;

class PedidoController extends Controller
{
    //Control de Permisos
    public function __construct()
    {
        $this->middleware('PermisoAdmin', ['only' => ['destroy']]);
    }

    //Listado General de Pedidos
    public function index(Request $request)
    {
        try {
            // Inicia la consulta con el modelo Articulo
            $query = Pedido::query();

            $query->with('cliente');
            $query->with('pedidoDetalles');
            $query->with('pedidoDetalles.articulo');
            $query->with('pedidoDetalles.colocacion');

            // Filtra por nombre cliente
            if ($request->has('nombre_cliente')) {
                $query->where('cliente.nombre', 'LIKE', '%' . $request->nombre_cliente . '%');
            }

            // Filtra por nombre cliente
            if ($request->has('tipo_cliente')) {
                $query->where('cliente.tipo_cliente', 'LIKE', '%' . $request->tipo_cliente . '%');
            }

            // Filtra por nombre cliente
            if ($request->has('codigo_barra_colocacion')) {
                $query->where('pedidoDetalles.colocacion.codigo_barra_colocacion', 'LIKE', '%' . $request->tipo_cliente . '%');
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

    //Registrar Pedido
    public function store(Request $request)
    {
        try {

            //Validar datos
            $request->validate([
                'cliente_id' => 'required|exists:tblcliente,id',
                'detalles' => 'required|array',
                'detalles.*.articulo_id' => 'required|exists:tblarticulo,id',
                'detalles.*.colocacion_id' => 'required|exists:tblcolocacion,id',
                'detalles.*.cantidad' => 'required|integer|min:1',
            ]);

            //Registrar pedido
            $pedido = Pedido::create([
                'cliente_id' => $request->cliente_id,
            ]);

            $detallesAgrupados = [];

            foreach ($request->detalles as $detalle) {
                $key = $detalle['articulo_id'] . '_' . $detalle['colocacion_id'];

                if (isset($detallesAgrupados[$key])) {
                    // Si ya existe un detalle para esta combinación, acumular la cantidad
                    $detallesAgrupados[$key]['cantidad'] += $detalle['cantidad'];
                } else {
                    // Si no existe, agregar el nuevo detalle
                    $detallesAgrupados[$key] = [
                        'articulo_id' => $detalle['articulo_id'],
                        'colocacion_id' => $detalle['colocacion_id'],
                        'cantidad' => $detalle['cantidad'],
                    ];
                }
            }

            // Registrar detalles del pedido con las cantidades agrupadas
            foreach ($detallesAgrupados as $detalleAgrupado) {
                PedidoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'articulo_id' => $detalleAgrupado['articulo_id'],
                    'colocacion_id' => $detalleAgrupado['colocacion_id'],
                    'cantidad' => $detalleAgrupado['cantidad'],
                ]);
            }

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $pedido->load('pedidoDetalles')
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    // Show a specific pedido with its details
    public function show($id)
    {
        try {
            //Consulta de empleado
            $pedido = Pedido::with('pedidoDetalles')->findOrFail($id);

            //Retorno de respuesta satisfactoria
            return response()->json([
                "estado" => true,
                "data" => $pedido
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "mensaje" => $th->getMessage()
            ], 500);
        }
    }

    // Update a specific pedido (not including detalles)
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'cliente_id' => 'required|exists:tblcliente,id',
            'detalles' => 'required|array',
            'detalles.*.articulo_id' => 'required|exists:tblarticulo,id',
            'detalles.*.colocacion_id' => 'required|exists:tblcolocacion,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

        $pedido = Pedido::findOrFail($id);

        $pedido->update($request->all());

        //Limpiar registros previos
        PedidoDetalle::where("pedido_id", $id)->delete();

        $detallesAgrupados = [];

        foreach ($request->detalles as $detalle) {
            $key = $detalle['articulo_id'] . '_' . $detalle['colocacion_id'];

            if (isset($detallesAgrupados[$key])) {
                // Si ya existe un detalle para esta combinación, acumular la cantidad
                $detallesAgrupados[$key]['cantidad'] += $detalle['cantidad'];
            } else {
                // Si no existe, agregar el nuevo detalle
                $detallesAgrupados[$key] = [
                    'articulo_id' => $detalle['articulo_id'],
                    'colocacion_id' => $detalle['colocacion_id'],
                    'cantidad' => $detalle['cantidad'],
                ];
            }
        }

        // Registrar detalles del pedido con las cantidades agrupadas
        foreach ($detallesAgrupados as $detalleAgrupado) {
            PedidoDetalle::create([
                'pedido_id' => $pedido->id,
                'articulo_id' => $detalleAgrupado['articulo_id'],
                'colocacion_id' => $detalleAgrupado['colocacion_id'],
                'cantidad' => $detalleAgrupado['cantidad'],
            ]);
        }
        
        //Retorno de respuesta satisfactoria
        return response()->json([
            "estado" => true,
            "data" => $pedido->with("pedidoDetalles")
        ]);
    }

    //Eliminar un Pedido
    public function destroy($id)
    {
        try {
            //Eliminar Pedido
            $pedido = Pedido::findOrFail($id);
            $pedido->pedidoDetalles()->delete();
            $pedido->delete();

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
