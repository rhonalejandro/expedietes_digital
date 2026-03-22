<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken()
            ?? $request->header('X-Api-Token')
            ?? $request->query('token');

        if (!$token || $token !== config('services.chatwoot.widget_token')) {
            return response()->json(['error' => 'No autorizado.'], 401);
        }

        return $next($request);
    }
}
