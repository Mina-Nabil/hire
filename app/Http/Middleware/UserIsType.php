<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserIsType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $types = explode('|', $type);
        $user = Auth::user();
        if(!in_array($user->type, $types)){
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
