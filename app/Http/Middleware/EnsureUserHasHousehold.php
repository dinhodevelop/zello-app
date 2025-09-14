<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasHousehold
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se o usuário não tem household e não está na rota de household
        if ($user && !$user->household_id && !$request->routeIs('household.*')) {
            return redirect()->route('household.index');
        }

        return $next($request);
    }
}
