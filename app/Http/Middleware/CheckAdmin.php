<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return redirect('/'); // Yetkisiz ise ana sayfaya yönlendir
        }

        return $next($request); // Devam et
    }
}
