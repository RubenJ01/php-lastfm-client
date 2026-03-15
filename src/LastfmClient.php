<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient;

use Rjds\PhpLastfmClient\Exception\LastfmApiException;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\Http\LastfmHttpClient;
use Rjds\PhpLastfmClient\Service\LibraryService;
use Rjds\PhpLastfmClient\Service\TrackService;
use Rjds\PhpLastfmClient\Service\UserService;

final class LastfmClient
{
    private const string BASE_URL = 'https://ws.audioscrobbler.com/2.0/';

    public function __construct(
        private readonly string $apiKey,
        private readonly HttpClientInterface $httpClient = new LastfmHttpClient(),
        private readonly ?string $apiSecret = null,
        private readonly ?string $sessionKey = null,
    ) {
    }

    /**
     * Access user-related API methods.
     */
    public function user(): UserService
    {
        return new UserService($this);
    }

    /**
     * Access library-related API methods.
     */
    public function library(): LibraryService
    {
        return new LibraryService($this);
    }

    /**
     * Access track-related API methods.
     */
    public function track(): TrackService
    {
        return new TrackService($this);
    }

    /**
     * Make a raw GET API call to the Last.fm API.
     *
     * @param string $method The API method (e.g. 'user.getinfo')
     * @param array<string, string> $params Additional query parameters
     * @return array<string, mixed> The decoded JSON response
     *
     * @throws LastfmApiException when the API returns an error
     */
    public function call(string $method, array $params = []): array
    {
        $queryParams = array_merge($params, [
            'method' => $method,
            'api_key' => $this->apiKey,
            'format' => 'json',
        ]);

        $url = self::BASE_URL . '?' . http_build_query($queryParams);

        $body = $this->httpClient->get($url);

        return $this->decodeResponse($body);
    }

    /**
     * Make an authenticated POST API call to the Last.fm API.
     *
     * Generates the required API signature and includes the session key.
     *
     * @param string $method The API method (e.g. 'track.scrobble')
     * @param array<string, string> $params Additional parameters
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
                'Session key is required for authenticated calls.'
            );
        }

        $params = array_merge($params, [
            'method' => $method,
            'api_key' => $this->apiKey,
            'sk' => $this->sessionKey,
        ]);

        $params['api_sig'] = $this->generateSignature($params);
        $params['format'] = 'json';

        $body = $this->httpClient->post(self::BASE_URL, $params);

        return $this->decodeResponse($body);
    }

    /**
     * Generate an API method signature.
     *
     * @param array<string, string> $params Parameters to sign (excluding 'format')
     *
     * @see https://www.last.fm/api/authspec#_8-signing-calls
     */
    private function generateSignature(array $params): string
    {
        ksort($params);

        $signature = '';
        foreach ($params as $key => $value) {
            $signature .= $key . $value;
        }

        $signature .= $this->apiSecret;

        return md5($signature);
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
