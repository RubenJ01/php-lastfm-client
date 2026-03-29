<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Http;

/**
 * Default HTTP transport using PHP streams ({@see file_get_contents}).
 *
 * Inject a custom fetcher closure (same signature as {@see file_get_contents}) for tests.
 */
final class StreamHttpTransport implements HttpTransportInterface
{
    /**
     * @param ?\Closure $fetchContents Optional {@see file_get_contents} substitute (same signature).
     */
    public function __construct(
        private readonly ?\Closure $fetchContents = null,
    ) {
    }

    public function send(string $method, string $url, string $headers, ?string $body): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'ignore_errors' => true,
                'header' => $headers,
                'content' => $body,
            ],
        ]);

        $fetch = $this->fetchContents ?? self::defaultFetch(...);
        $response = $fetch($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException(
                "Failed to perform HTTP {$method} request to: {$url}"
            );
        }

        return $response;
    }

    /**
     * @param resource|null $context
     */
    private static function defaultFetch(string $filename, bool $useIncludePath, $context): string|false
    {
        return @file_get_contents($filename, $useIncludePath, $context);
    }
}
