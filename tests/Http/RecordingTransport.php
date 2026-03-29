<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Http;

use Rjds\PhpLastfmClient\Http\HttpTransportInterface;

final class RecordingTransport implements HttpTransportInterface
{
    public string $lastMethod = '';

    public string $lastUrl = '';

    public string $lastHeaders = '';

    public ?string $lastBody = null;

    public string $responseBody = '';

    public function send(string $method, string $url, string $headers, ?string $body): string
    {
        $this->lastMethod = $method;
        $this->lastUrl = $url;
        $this->lastHeaders = $headers;
        $this->lastBody = $body;

        return $this->responseBody;
    }
}
