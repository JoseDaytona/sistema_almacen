<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoDetalle;

class PedidoController extends Controller
{
    public function index()
    {
        try {
            $response = Pedido::with('pedidoDetalles')->get();
            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Store a new pedido along with its details
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'detalles' => 'required|array',
            'detalles.*.articulo_id' => 'required|exists:articulos,id',
            'detalles.*.colocacion_id' => 'required|exists:colocaciones,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

        // Create the pedido
        $pedido = Pedido::create([
            'cliente_id' => $request->cliente_id,
        ]);

        // Add the pedido details
        foreach ($request->detalles as $detalle) {
            PedidoDetalle::create([
                'pedido_id' => $pedido->id,
                'articulo_id' => $detalle['articulo_id'],
                'colocacion_id' => $detalle['colocacion_id'],
                'cantidad' => $detalle['cantidad'],
            ]);
        }

        return response()->json($pedido->load('pedidoDetalles'), 201);
    }

    // Show a specific pedido with its details
    public function show($id)
    {
        $pedido = Pedido::with('pedidoDetalles')->findOrFail($id);
        return response()->json($pedido);
    }

    // Update a specific pedido (not including detalles)
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->update($request->all());

        return response()->json($pedido, 200);
    }

    // Delete a specific pedido and its details
    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->pedidoDetalles()->delete(); // Delete associated pedido_detalles
        $pedido->delete();

        return response()->json(null, 204);
    }
}
