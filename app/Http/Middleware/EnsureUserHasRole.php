<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_if(! $user, Response::HTTP_UNAUTHORIZED);
        abort_if(! $user->hasRole(...$roles), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
