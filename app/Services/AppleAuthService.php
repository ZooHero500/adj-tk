<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class AppleAuthService
{
    public const CACHE_KEY = 'loops:s:apple_auth:';

    public function getApplePublicKeys()
    {
        return Cache::remember(self::CACHE_KEY.'pubkeys:v1', now()->addDay(), function () {
            return Http::get('https://appleid.apple.com/auth/keys')->json();
        });
    }

    public function generateClientSecret(): string
    {
        $privateKey = InMemory::file(
            storage_path(config('services.apple.private_key_path'))
        );

        $config = Configuration::forSymmetricSigner(
            new Sha256,
            $privateKey
        );

        $now = new \DateTimeImmutable;

        return $config->builder()
            ->issuedBy(config('services.apple.team_id'))
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->permittedFor('https://appleid.apple.com')
            ->withHeader('kid', config('services.apple.key_id'))
            ->relatedTo(config('services.apple.bundle_id'))
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }

    /**
     * Revoke Apple tokens — call this when user deletes their PornTk account.
     */
    public function revokeToken(string $appleRefreshToken): bool
    {
        $response = Http::asForm()->post('https://appleid.apple.com/auth/revoke', [
            'client_id' => config('services.apple.bundle_id'),
            'client_secret' => $this->generateClientSecret(),
            'token' => $appleRefreshToken,
            'token_type_hint' => 'refresh_token',
        ]);

        return $response->successful();
    }
}
