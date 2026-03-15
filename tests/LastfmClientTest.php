<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Exception\LastfmApiException;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;
use Rjds\PhpLastfmClient\Service\LibraryService;
use Rjds\PhpLastfmClient\Service\TrackService;
use Rjds\PhpLastfmClient\Service\UserService;

final class LastfmClientTest extends TestCase
{
    #[Test]
    public function itReturnsUserService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(UserService::class, $client->user());
    }

    #[Test]
    public function itReturnsLibraryService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(LibraryService::class, $client->library());
    }

    #[Test]
    public function itReturnsTrackService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(TrackService::class, $client->track());
    }

    #[Test]
    public function itCallsApiWithCorrectParameters(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);

                $host = parse_url($url, PHP_URL_HOST);
                $this->assertSame('ws.audioscrobbler.com', $host);

                parse_str($query, $queryParams);

                $this->assertSame('user.getinfo', $queryParams['method']);
                $this->assertSame('my-api-key', $queryParams['api_key']);
                $this->assertSame('json', $queryParams['format']);
                $this->assertSame('rj', $queryParams['user']);

                return true;
            }))
            ->willReturn('{"user": {}}');

        $client = new LastfmClient('my-api-key', $httpClient);
        $client->call('user.getinfo', ['user' => 'rj']);
    }

    #[Test]
    public function itThrowsOnApiError(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn('{"error": 6, "message": "User not found"}');

        $client = new LastfmClient('test-api-key', $httpClient);

        $this->expectException(LastfmApiException::class);
        $this->expectExceptionMessage('User not found');
        $this->expectExceptionCode(6);

        $client->call('user.getinfo', ['user' => 'nonexistent']);
    }

    #[Test]
    public function itThrowsOnInvalidJsonResponse(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn('not valid json');

        $client = new LastfmClient('test-api-key', $httpClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to decode Last.fm API response');

        $client->call('user.getinfo');
    }

    #[Test]
    public function itUsesDefaultMessageWhenApiErrorHasNoMessage(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn('{"error": 10}');

        $client = new LastfmClient('test-api-key', $httpClient);

        $this->expectException(LastfmApiException::class);
        $this->expectExceptionMessage('Unknown API error');
        $this->expectExceptionCode(10);

        $client->call('user.getinfo');
    }

    #[Test]
    public function itReturnsFullResponseArray(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn('{"user": {"name": "RJ"}, "extra": "data"}');

        $client = new LastfmClient('test-api-key', $httpClient);
        $result = $client->call('user.getinfo');

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('extra', $result);
        $this->assertSame('data', $result['extra']);
    }

    #[Test]
    public function apiExceptionExposesErrorCode(): void
    {
        $exception = new LastfmApiException('Invalid API key', 10);

        $this->assertSame(10, $exception->getApiErrorCode());
        $this->assertSame('Invalid API key', $exception->getMessage());
    }

    #[Test]
    public function itCallsAuthenticatedWithSignatureAndSessionKey(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->stringContains('audioscrobbler'),
                $this->callback(function (array $data): bool {
                    $this->assertSame('track.scrobble', $data['method']);
                    $this->assertSame('my-key', $data['api_key']);
                    $this->assertSame('my-session', $data['sk']);
                    $this->assertSame('json', $data['format']);
                    $this->assertArrayHasKey('api_sig', $data);
                    $this->assertSame('testvalue', $data['custom']);

                    return true;
                }),
            )
            ->willReturn('{"scrobbles": {}}');

        $client = new LastfmClient(
            apiKey: 'my-key',
            httpClient: $httpClient,
            apiSecret: 'my-secret',
            sessionKey: 'my-session',
        );

        $client->callAuthenticated('track.scrobble', ['custom' => 'testvalue']);
    }

    #[Test]
    public function itGeneratesCorrectApiSignature(): void
    {
        $capturedData = [];

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->anything(),
                $this->callback(function (array $data) use (&$capturedData): bool {
                    $capturedData = $data;
                    return true;
                }),
            )
            ->willReturn('{"result": {}}');

        $client = new LastfmClient(
            apiKey: 'testkey',
            httpClient: $httpClient,
            apiSecret: 'testsecret',
            sessionKey: 'testsk',
        );

        $client->callAuthenticated('test.method', ['foo' => 'bar']);

        // Signature = md5 of sorted params (without format) + secret
        // Params: api_key=testkey, foo=bar, method=test.method, sk=testsk
        // Sorted: api_keytestkeyfoobarmethod..., etc.
        $expected = md5(
            'api_keytestkey'
            . 'foobar'
            . 'methodtest.method'
            . 'sktestsk'
            . 'testsecret'
        );

        $this->assertSame($expected, $capturedData['api_sig']);
    }

    #[Test]
    public function itThrowsWhenApiSecretIsMissing(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API secret is required');

        $client->callAuthenticated('track.scrobble');
    }

    #[Test]
    public function itThrowsWhenSessionKeyIsMissing(): void
    {
        $client = new LastfmClient(
            apiKey: 'test-api-key',
            apiSecret: 'secret',
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Session key is required');

        $client->callAuthenticated('track.scrobble');
    }

    #[Test]
    public function itHandlesApiErrorOnAuthenticatedPost(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn('{"error": 9, "message": "Invalid session key"}');

        $client = new LastfmClient(
            apiKey: 'key',
            httpClient: $httpClient,
            apiSecret: 'secret',
            sessionKey: 'bad-sk',
        );

        $this->expectException(LastfmApiException::class);
        $this->expectExceptionMessage('Invalid session key');
        $this->expectExceptionCode(9);

        $client->callAuthenticated('track.scrobble');
    }

    #[Test]
    public function itHandlesInvalidJsonOnAuthenticatedPost(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn('not json');

        $client = new LastfmClient(
            apiKey: 'key',
            httpClient: $httpClient,
            apiSecret: 'secret',
            sessionKey: 'sk',
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to decode Last.fm API response');

        $client->callAuthenticated('track.scrobble');
    }
}
