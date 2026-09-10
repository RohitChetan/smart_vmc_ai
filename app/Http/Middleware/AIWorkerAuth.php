<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AIWorkerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('services.ai_worker.key');
        $providedKey = $request->header('X-AI-Worker-Key');

        if (!$expectedKey || !$providedKey || !hash_equals($expectedKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized AI worker.',
            ], 401);
        }

        return $next($request);
    }
}
