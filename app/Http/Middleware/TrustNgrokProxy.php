<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustNgrokProxy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Trust ngrok's reverse proxy headers
        if ($this->isNgrok()) {
            $request->setTrustedProxies(
                [$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'],
                Request::HEADER_X_FORWARDED_ALL
            );
        }

        return $next($request);
    }

    /**
     * Check if the request is coming from ngrok.
     *
     * @return bool
     */
    private function isNgrok()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';

        // Check if ngrok tunneling
        return strpos($host, 'ngrok-free.dev') !== false || 
               strpos($host, 'ngrok.io') !== false ||
               strpos($userAgent, 'ngrok') !== false;
    }
}
