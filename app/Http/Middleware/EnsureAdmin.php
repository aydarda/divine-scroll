<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // 404, not 403, so the admin area doesn't advertise that it exists.
        abort_unless($request->user()?->is_admin, 404);

        return $next($request);
    }
}
