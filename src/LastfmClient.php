<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient;

use Rjds\PhpLastfmClient\Exception\LastfmApiException;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\Http\LastfmHttpClient;
use Rjds\PhpLastfmClient\Service\AuthService;
use Rjds\PhpLastfmClient\Service\ChartService;
use Rjds\PhpLastfmClient\Service\LibraryService;
use Rjds\PhpLastfmClient\Service\TrackService;
use Rjds\PhpLastfmClient\Service\UserService;

final class LastfmClient
{
    private const string BASE_URL = 'https://ws.audioscrobbler.com/2.0/';

    private ?AuthService $authService = null;
    private ?ChartService $chartService = null;
    private ?UserService $userService = null;
    private ?LibraryService $libraryService = null;
    private ?TrackService $trackService = null;

    public function __construct(
        private readonly string $apiKey,
        private readonly ?string $apiSecret = null,
        private ?string $sessionKey = null,
        private readonly HttpClientInterface $httpClient = new LastfmHttpClient(),
    ) {
    }

    /**
     * Access authentication-related API methods.
     */
    public function auth(): AuthService
    {
        return $this->authService ??= new AuthService($this);
    }

    /**
     * Access chart-related API methods.
     */
    public function chart(): ChartService
    {
        return $this->chartService ??= new ChartService($this);
    }

    /**
     * Access user-related API methods.
     */
    public function user(): UserService
    {
        return $this->userService ??= new UserService($this);
    }

    /**
     * Access library-related API methods.
     */
    public function library(): LibraryService
    {
        return $this->libraryService ??= new LibraryService($this);
    }

    /**
     * Access track-related API methods.
     */
    public function track(): TrackService
    {
        return $this->trackService ??= new TrackService($this);
    }

    /**
     * Set the session key for authenticated calls.
     *
     * Useful after completing the authentication flow via auth()->getSession().
     */
    public function setSessionKey(string $sessionKey): void
    {
        $this->sessionKey = $sessionKey;
    }

    /**
     * Get the API key.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Make a GET API call to the Last.fm API.
     *
     * @param string $method The API method (e.g. 'user.getinfo')
     * @param array<string, string|int> $params Additional query parameters
     * @return array<string, mixed> The decoded JSON response
     *
     * @throws LastfmApiException when the API returns an error
     */
    public function call(string $method, array $params = []): array
    {
        $params = $this->withBaseParams($method, $params);
        $params['format'] = 'json';

        $url = self::BASE_URL . '?' . http_build_query($params);

        return $this->decodeResponse($this->httpClient->get($url));
    }

    /**
     * Make a signed GET API call to the Last.fm API.
     *
     * Used for methods that require a signature but not a session key
     * (e.g. auth.getSession).
     *
     * @param string $method The API method (e.g. 'auth.getSession')
     * @param array<string, string|int> $params Additional parameters
     * @return array<string, mixed> The decoded JSON response
     *
     * @throws \RuntimeException when API secret is not configured
     * @throws LastfmApiException when the API returns an error
     */
    public function callSigned(string $method, array $params = []): array
    {
        $params = $this->withBaseParams($method, $params);
        $params = $this->signParams($params);

        $url = self::BASE_URL . '?' . http_build_query($params);

        return $this->decodeResponse($this->httpClient->get($url));
    }

    /**
     * Make an authenticated POST API call to the Last.fm API.
     *
     * Generates the required API signature and includes the session key.
     *
     * @param string $method The API method (e.g. 'track.scrobble')
     * @param array<string, string|int> $params Additional parameters
     * @return array<string, mixed> The decoded JSON response
     *
     * @throws \RuntimeException when API secret or session key is not configured
     * @throws LastfmApiException when the API returns an error
     */
    public function callAuthenticated(string $method, array $params = []): array
    {
        if ($this->apiSecret === null) {
            throw new \RuntimeException(
                'API secret is required for authenticated calls.'
            );
        }

        if ($this->sessionKey === null) {
            throw new \RuntimeException(
                'Session key is required for authenticated calls. Use setSessionKey() or pass it in the constructor.'
            );
        }

        $params = $this->withBaseParams($method, $params);
        $params['sk'] = $this->sessionKey;
        $params = $this->signParams($params);

        return $this->decodeResponse($this->httpClient->post(self::BASE_URL, $params));
    }

    /**
     * Merge base API parameters (method, api_key) into the given params.
     *
     * @param array<string, string|int> $params
     * @return array<string, string|int>
     */
    private function withBaseParams(string $method, array $params): array
    {
        return array_merge($params, [
            'method' => $method,
            'api_key' => $this->apiKey,
        ]);
    }

    /**
     * Sign the parameters and add 'api_sig' and 'format'.
     *
     * @param array<string, string|int> $params Parameters to sign (must not include 'format')
     * @return array<string, string|int>
     *
     * @throws \RuntimeException when API secret is not configured
     */
    private function signParams(array $params): array
    {
        if ($this->apiSecret === null) {
            throw new \RuntimeException(
                'API secret is required for signed/authenticated calls.'
            );
        }

        ksort($params);

        $signature = '';
        foreach ($params as $key => $value) {
            $signature .= $key . $value;
        }

        $params['api_sig'] = md5($signature . $this->apiSecret);
        $params['format'] = 'json';

        return $params;
    }

    /**
     * Decode and validate a JSON response from the Last.fm API.
     *
     * @return array<string, mixed>
     *
     * @throws \RuntimeException when the response cannot be decoded
     * @throws LastfmApiException when the API returns an error
     */
    private function decodeResponse(string $body): array
    {
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new \RuntimeException('Failed to decode Last.fm API response');
        }

        if (isset($data['error']) && is_int($data['error'])) {
            $message = isset($data['message']) && is_string($data['message'])
                ? $data['message']
                : 'Unknown API error';

            throw new LastfmApiException($message, $data['error']);
        }

        /** @var array<string, mixed> $data */
        return $data;
    }
}
