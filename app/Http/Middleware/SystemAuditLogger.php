<?php

namespace App\Http\Middleware;

use App\Models\SystemAuditLog;
use App\Support\IpGeo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemAuditLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $path = '/' . ltrim($request->path(), '/');
        $skipPrefixes = ['/assets', '/storage', '/favicon', '/css', '/js', '/build'];

        foreach ($skipPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $response;
            }
        }

        $payload = null;
        if (in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $payload = $request->except([
                'password',
                'password_confirmation',
                'current_password',
                '_token',
            ]);
        }

        $country = IpGeo::countryForIp($request->ip());

        SystemAuditLog::create([
            'user_id' => optional($request->user())->id,
            'action' => 'request',
            'description' => null,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route_name' => optional($request->route())->getName(),
            'ip_address' => $request->ip(),
            'country' => $country,
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'status_code' => $response->getStatusCode(),
            'payload' => $payload,
        ]);

        return $response;
    }
}
