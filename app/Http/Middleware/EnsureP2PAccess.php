<?php

namespace App\Http\Middleware;

use App\Support\P2PAccess;
use Closure;
use Illuminate\Http\Request;

class EnsureP2PAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!P2PAccess::allows(auth()->user())) {
            if ($request->expectsJson()) {
                abort(403);
            }

            return redirect()
                ->route('user.p2p.coming-soon')
                ->with('notify', [['info', 'هذه المنطقة قيد التشغيل حالياً']]);
        }

        return $next($request);
    }
}
