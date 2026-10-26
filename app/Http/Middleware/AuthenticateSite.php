<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSite
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $bearer = $request->bearerToken();

        if ($bearer === null || $bearer === '') {
            abort(401, 'Missing site API token.');
        }

        $accessToken = PersonalAccessToken::findToken($bearer);

        if ($accessToken === null || ! $accessToken->tokenable instanceof Site) {
            abort(401, 'Invalid site API token.');
        }

        /** @var Site $site */
        $site = $accessToken->tokenable;

        if (! $site->is_active) {
            abort(403, 'This site is inactive.');
        }

        foreach ($abilities as $ability) {
            if (! $accessToken->can($ability)) {
                abort(403, 'Token missing required ability.');
            }
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();
        $site->touchLastSeen();

        $request->attributes->set('site', $site);
        $request->setUserResolver(fn (): Site => $site);

        return $next($request);
    }
}
