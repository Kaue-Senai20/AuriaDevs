<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Token simples compartilhado entre o Laravel e o bot de WhatsApp.
// Enviar no header X-API-Token (ou ?api_token=). Valor em API_TOKEN no .env.
class ApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $esperado = (string) config('app.api_token', env('API_TOKEN', ''));
        $recebido = (string) ($request->header('X-API-Token') ?? $request->query('api_token', ''));

        if ($esperado === '' || !hash_equals($esperado, $recebido)) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        return $next($request);
    }
}
