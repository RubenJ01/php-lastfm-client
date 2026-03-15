<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient;

use Rjds\PhpLastfmClient\Exception\LastfmApiException;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\Http\LastfmHttpClient;
use Rjds\PhpLastfmClient\Service\LibraryService;
use Rjds\PhpLastfmClient\Service\UserService;

final class LastfmClient
{
    private const string BASE_URL = 'https://ws.audioscrobbler.com/2.0/';

    public function __construct(
        private readonly string $apiKey,
        private readonly HttpClientInterface $httpClient = new LastfmHttpClient(),
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
     * Make a raw API call to the Last.fm API.
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
