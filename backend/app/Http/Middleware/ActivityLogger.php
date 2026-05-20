<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function handle(Request $request, Closure $next, string $module): Response
    {
        $response = $next($request);

        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->activityLog->log(
                strtolower($request->method()),
                $module,
                null,
                ['path' => $request->path(), 'status' => $response->getStatusCode()]
            );
        }

        return $response;
    }
}
