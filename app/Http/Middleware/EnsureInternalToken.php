<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInternalToken
{
    public function handle(Request $request, Closure $next, string $tokenConfigKey = 'cron'): Response
    {
        $expected = (string) config('internal_tokens.' . $tokenConfigKey, '');

        if ($expected === '') {
            return response('Forbidden', 403);
        }

        $provided = (string) ($request->header('X-Internal-Token')
            ?? $request->header('X-Cron-Token')
            ?? $request->query('token')
            ?? '');

        if (!hash_equals($expected, $provided)) {
            return response('Forbidden', 403);
        }

        return $next($request);
    }
}
