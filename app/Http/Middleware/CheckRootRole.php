<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRootRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Kiểm tra xem có vai trò "root" nào tồn tại trong cơ sở dữ liệu hay không
        $rootRole = User::where('role', 'root')->first();

        if ($rootRole) {
            return response()->json(['error' => 'Role "root" already exists.'], 403);
        }

        return $next($request);
    }
}
