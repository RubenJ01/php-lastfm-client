<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Http;

interface HttpClientInterface
{
    /**
     * Perform a GET request and return the response body.
     *
     * @throws \RuntimeException on network or connection errors
     */
    public function get(string $url): string;

    /**
     * Perform a POST request with form data and return the response body.
     *
     * @param array<string, string> $data Form data to send
     *
     * @throws \RuntimeException on network or connection errors
     */
    public function post(string $url, array $data): string;
}
