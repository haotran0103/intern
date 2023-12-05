<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRootUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        dd($user);
        if (Auth::check()) {
            $user = Auth::user();

            if ($user && $user->role !== 'root') {
                return response()->json(['error' => 'Bạn không có quyền thực hiện hành động này.'], 403);
            }

            // Nếu người dùng xác thực và có quyền truy cập, tiếp tục xử lý request
            return $next($request);
        }

        // Nếu không có người dùng xác thực, có thể chuyển hướng hoặc trả về lỗi 401 Unauthorized
        return response()->json(['error' => 'Bạn chưa đăng nhập.'], 401);
        
    }
}
