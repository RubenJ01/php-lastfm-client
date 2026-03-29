<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Http;

final class LastfmHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly HttpTransportInterface $transport = new StreamHttpTransport(),
    ) {
    }

    public function get(string $url): string
    {
        return $this->request('GET', $url);
    }

    /**
     * @param array<string, string|int> $data
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

        return $this->transport->send($method, $url, $headers, $body);
    }
}
