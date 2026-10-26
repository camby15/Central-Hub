<?php

namespace App\Actions;

use App\Models\Site;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

class RotateSiteKey
{
    /**
     * @return array{token: NewAccessToken, plain_text: string}
     */
    public function handle(Site $site, string $tokenName = 'api'): array
    {
        $site->tokens()->delete();

        $token = $site->createToken($tokenName, [
            'tickets:create',
            'tickets:read',
            'tickets:reply',
        ]);

        $plainText = $token->plainTextToken;
        $randomPart = explode('|', $plainText, 2)[1] ?? $plainText;

        $site->forceFill([
            'api_key_prefix' => Str::lower($site->slug).'_'.Str::substr($randomPart, 0, 8),
            'hmac_secret' => $site->hmac_secret ?: Str::random(32),
        ])->save();

        return [
            'token' => $token,
            'plain_text' => $plainText,
        ];
    }
}
