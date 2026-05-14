<?php

declare(strict_types=1);

namespace Juling\Foundation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Juling\Foundation\Constants\RequestConst;
use Symfony\Component\HttpFoundation\Response;

class Trace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        Context::addHidden(RequestConst::TraceTime, $startTime);

        $traceId = $request->header(RequestConst::RequestId, Str::uuid()->toString());
        Context::addHidden(RequestConst::TraceId, $traceId);

        $response = $next($request);
        $response->headers->set(RequestConst::RequestId, $traceId);

        $content = $response->getContent();
        if (json_validate($content)) {
            $content = json_encode(json_decode($content, true), JSON_UNESCAPED_UNICODE);
        }

        Log::info($content);

        return $response;
    }
}
