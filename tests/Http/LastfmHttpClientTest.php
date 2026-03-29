<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Http;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Http\LastfmHttpClient;

final class LastfmHttpClientTest extends TestCase
{
    #[Test]
    public function getDelegatesToTransportWithGetMethodAndUserAgent(): void
    {
        $recording = new RecordingTransport();
        $client = new LastfmHttpClient($recording);

        $recording->responseBody = '{"user":{}}';
        $result = $client->get('https://ws.audioscrobbler.com/2.0/?method=user.getinfo');

        $this->assertSame('{"user":{}}', $result);
        $this->assertSame('GET', $recording->lastMethod);
        $this->assertSame('https://ws.audioscrobbler.com/2.0/?method=user.getinfo', $recording->lastUrl);
        $this->assertStringContainsString('User-Agent: php-lastfm-client/1.0', $recording->lastHeaders);
        $this->assertStringNotContainsString('Content-Type:', $recording->lastHeaders);
        $this->assertNull($recording->lastBody);
    }

    #[Test]
    public function postDelegatesToTransportWithEncodedBodyAndContentType(): void
    {
        $recording = new RecordingTransport();
        $client = new LastfmHttpClient($recording);

        $recording->responseBody = '{"ok":1}';
        $result = $client->post('https://ws.audioscrobbler.com/2.0/', [
            'method' => 'track.scrobble',
            'api_key' => 'k',
        ]);

        $this->assertSame('{"ok":1}', $result);
        $this->assertSame('POST', $recording->lastMethod);
        $this->assertSame('https://ws.audioscrobbler.com/2.0/', $recording->lastUrl);
        $this->assertStringContainsString('User-Agent: php-lastfm-client/1.0', $recording->lastHeaders);
        $this->assertStringContainsString('Content-Type: application/x-www-form-urlencoded', $recording->lastHeaders);
        $this->assertSame('method=track.scrobble&api_key=k', $recording->lastBody);
    }
}
