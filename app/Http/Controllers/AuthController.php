<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
/**
 * @OA\Info(title="My First API", version="0.1")
 */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {

            $request->validate([
                'usuario' => 'required|string|exists:tblUsuario,usuario',
                'password' => 'required|string'
            ]);

            $usuario = $request->get("usuario");
            $password = $request->get("password");

            if(autenticar($usuario, $password))
            {
                $token = $usuario->createToken("auth_token")->plainTextToken;

                return response()->json([
                    "message" => "Sesion Iniciada",
                    "data" => [
                        "token_type" => "Bearer",
                        "access_token" => $token
                    ]
                ]);
            }
            else
            {
                return response()->json([
                    "message" => "Credenciales Incorrectas"
                ], 401);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                "estado" => false,
                "message" => $th->getMessage()
            ], 401);
        }
    }

    public function unauthorized()
    {
        return response()->json([
            "message" => "No autorizado"
        ], 401);
    }
}
