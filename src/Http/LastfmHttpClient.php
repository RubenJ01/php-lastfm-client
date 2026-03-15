<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Http;

final class LastfmHttpClient implements HttpClientInterface
{
    public function get(string $url): string
    {
        return $this->request('GET', $url);
    }

    /**
     * @param array<string, string> $data
     */
    public function post(string $url, array $data): string
    {
        return $this->request('POST', $url, http_build_query($data));
    }

    private function request(string $method, string $url, ?string $body = null): string
    {
        $headers = "User-Agent: php-lastfm-client/1.0\r\n";

        if ($body !== null) {
            $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'ignore_errors' => true,
                'header' => $headers,
                'content' => $body,
            ],
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException(
                "Failed to perform HTTP {$method} request to: {$url}"
            );
        }

        return $response;
    }
}
