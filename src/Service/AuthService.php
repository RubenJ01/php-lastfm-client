<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Auth\SessionDto;

final readonly class AuthService extends AbstractService
{
    private const string AUTH_URL = 'https://www.last.fm/api/auth/';

    /**
     * Get an unauthorized request token.
     *
     * This is the first step in the authentication flow.
     *
     * @see https://lastfm-docs.github.io/api-docs/auth/getToken/
     */
    public function getToken(): string
    {
        $response = $this->client->call('auth.gettoken');

        /** @var string $token */
        $token = $response['token'];

        return $token;
    }

    /**
     * Build the URL the user must visit to authorize the token.
     *
     * This is the second step in the authentication flow.
     * After the user visits this URL and grants access,
     * you can exchange the token for a session key.
     *
     * @param string $token The token from getToken()
     * @param string|null $callbackUrl Optional callback URL for web apps
     */
    public function getAuthUrl(string $token, ?string $callbackUrl = null): string
    {
        $params = [
            'api_key' => $this->client->getApiKey(),
            'token' => $token,
        ];

        if ($callbackUrl !== null) {
            $params['cb'] = $callbackUrl;
        }

        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Exchange an authorized token for a session key.
     *
     * This is the third step in the authentication flow.
     * The user must have authorized the token at the URL
     * returned by getAuthUrl() before calling this method.
     *
     * @param string $token The authorized token
     *
     * @see https://lastfm-docs.github.io/api-docs/auth/getSession/
     */
    public function getSession(string $token): SessionDto
    {
        $response = $this->client->callSigned('auth.getsession', [
            'token' => $token,
        ]);

        /** @var array<string, mixed> $sessionData */
        $sessionData = $response['session'];

        return $this->mapper->map($sessionData, SessionDto::class);
    }
}
