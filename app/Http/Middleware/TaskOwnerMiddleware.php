<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isAdmin()) {
            return $next($request);
        }
        if ($request->task->user_id == $request->user()->id) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized, this task belongs to another user',
        ], 403);
    }
}
