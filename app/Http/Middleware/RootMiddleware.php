<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
class RootMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            if ($user->role === 'root') {
                return $next($request);
            }
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }
        return response()->json(['message' => 'Bạn chưa đăng nhập'], 401);
    }
}