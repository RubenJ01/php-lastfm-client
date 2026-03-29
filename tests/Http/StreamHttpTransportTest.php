<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Http;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Http\StreamHttpTransport;

final class StreamHttpTransportTest extends TestCase
{
    #[Test]
    public function itThrowsRuntimeExceptionWhenFetcherReturnsFalse(): void
    {
        $transport = new StreamHttpTransport(static function (string $u, bool $i, $c): false {
            return false;
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to perform HTTP GET request to: https://example.test/');

        $transport->send('GET', 'https://example.test/', "User-Agent: x\r\n", null);
    }

    #[Test]
    public function itReturnsResponseFromInjectedFetcher(): void
    {
        $transport = new StreamHttpTransport(static function (string $u, bool $i, $c): string {
            return '{"status":"ok"}';
        });

        $this->assertSame(
            '{"status":"ok"}',
            $transport->send('POST', 'https://example.test/', "User-Agent: x\r\n", 'a=b'),
        );
    }

    #[Test]
    public function itReadsViaDefaultFileGetContents(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'lfm_http_');
        $this->assertNotFalse($path);
        file_put_contents($path, 'file-payload');

        try {
            $url = 'file://' . str_replace('\\', '/', $path);
            $transport = new StreamHttpTransport();

            $this->assertSame(
                'file-payload',
                $transport->send('GET', $url, "User-Agent: php-lastfm-client/1.0\r\n", null),
            );
        } finally {
            @unlink($path);
        }
    }
}
