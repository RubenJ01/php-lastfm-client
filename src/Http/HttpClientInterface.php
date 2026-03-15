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
}
