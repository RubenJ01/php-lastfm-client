<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Http;

final class LastfmHttpClient implements HttpClientInterface
{
    public function get(string $url): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
                'header' => "User-Agent: php-lastfm-client/1.0\r\n",
            ],
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException('Failed to perform HTTP request to: ' . $url);
        }

        return $response;
    }

    public function post(string $url, array $data): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'ignore_errors' => true,
                'header' => "User-Agent: php-lastfm-client/1.0\r\n"
                    . "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
            ],
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException('Failed to perform HTTP POST request to: ' . $url);
        }

        return $response;
    }
}
