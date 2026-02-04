<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSalonAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('salon_authenticated')) {
            if ($request->expectsJson() || $request->is('api/salon/*')) {
                return response()->json(['message' => 'Please log in to continue.'], 401);
            }

            return redirect()->route('salon.login');
        }

        return $next($request);
    }
}
