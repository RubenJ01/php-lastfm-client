<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Http;

interface HttpTransportInterface
{
    /**
     * Execute an HTTP request and return the response body.
     *
     * @param string $method HTTP verb (e.g. GET, POST)
     * @param string $url Request URL
     * @param string $headers Raw header block, including trailing CRLF lines
     *
     * @throws \RuntimeException when the transport fails (network, I/O, empty response where invalid, etc.)
     */
    public function send(string $method, string $url, string $headers, ?string $body): string;
}
