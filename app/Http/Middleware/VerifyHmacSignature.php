<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    private const int MaxSkewSeconds = 300;

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');

        if ($timestamp === null && $signature === null) {
            return $next($request);
        }

        if ($timestamp === null || $signature === null) {
            abort(401, 'Both X-Timestamp and X-Signature are required when signing requests.');
        }

        if (! ctype_digit($timestamp)) {
            abort(401, 'Invalid X-Timestamp.');
        }

        if (abs(now()->timestamp - (int) $timestamp) > self::MaxSkewSeconds) {
            abort(401, 'Request timestamp is too old or too far in the future.');
        }

        /** @var Site|null $site */
        $site = $request->attributes->get('site');

        if (! $site instanceof Site || blank($site->hmac_secret)) {
            abort(401, 'HMAC is not configured for this site.');
        }

        $expected = hash_hmac('sha256', $timestamp.$request->getContent(), $site->hmac_secret);

        $provided = str_starts_with($signature, 'sha256=')
            ? substr($signature, 7)
            : $signature;

        if (! hash_equals($expected, $provided)) {
            abort(401, 'Invalid HMAC signature.');
        }

        return $next($request);
    }
}
