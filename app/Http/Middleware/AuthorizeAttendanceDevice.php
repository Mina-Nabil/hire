<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeAttendanceDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $AuthdeveiceTypes = env('ATTENDANCE_DEVICES');
        $deviceType = $request->header('User-Agent');
        // if (!in_array($deviceType, $AuthdeveiceTypes)) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }
        return $next($request);
    }
}
