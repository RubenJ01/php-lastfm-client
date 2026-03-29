<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Exception\LastfmApiException;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;
use Rjds\PhpLastfmClient\Service\AuthService;
use Rjds\PhpLastfmClient\Service\ChartService;
use Rjds\PhpLastfmClient\Service\GeoService;
use Rjds\PhpLastfmClient\Service\LibraryService;
use Rjds\PhpLastfmClient\Service\TagService;
use Rjds\PhpLastfmClient\Service\TrackService;
use Rjds\PhpLastfmClient\Service\UserService;

final class LastfmClientTest extends TestCase
{
    // ── Service accessors ──────────────────────────────────────────

    #[Test]
    public function itReturnsAuthService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(AuthService::class, $client->auth());
    }

    #[Test]
    public function itReturnsSameAuthServiceInstance(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertSame($client->auth(), $client->auth());
    }

    #[Test]
    public function itReturnsChartService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(ChartService::class, $client->chart());
    }

    #[Test]
    public function itReturnsSameChartServiceInstance(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertSame($client->chart(), $client->chart());
    }

    #[Test]
    public function itReturnsGeoService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(GeoService::class, $client->geo());
    }

    #[Test]
    public function itReturnsSameGeoServiceInstance(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertSame($client->geo(), $client->geo());
    }

    #[Test]
    public function itReturnsUserService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(UserService::class, $client->user());
    }

    #[Test]
    public function itReturnsSameUserServiceInstance(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertSame($client->user(), $client->user());
    }

    #[Test]
    public function itReturnsLibraryService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(LibraryService::class, $client->library());
    }

    #[Test]
    public function itReturnsSameLibraryServiceInstance(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertSame($client->library(), $client->library());
    }

    #[Test]
    public function itReturnsTrackService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(TrackService::class, $client->track());
    }

    #[Test]
    public function itReturnsSameTrackServiceInstance(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertSame($client->track(), $client->track());
    }

    #[Test]
    public function itReturnsTagService(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertInstanceOf(TagService::class, $client->tag());
    }

    #[Test]
    public function itReturnsSameTagServiceInstance(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->assertSame($client->tag(), $client->tag());
    }

    #[Test]
    public function itExposesApiKey(): void
    {
        $client = new LastfmClient('my-special-key');

        $this->assertSame('my-special-key', $client->getApiKey());
    }

    // ── call() ─────────────────────────────────────────────────────

    #[Test]
    public function itCallsApiWithCorrectParameters(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $host = parse_url($url, PHP_URL_HOST);
                $this->assertSame('ws.audioscrobbler.com', $host);

                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str($query, $params);
                $this->assertSame('user.getinfo', $params['method']);
                $this->assertSame('my-api-key', $params['api_key']);
                $this->assertSame('json', $params['format']);
                $this->assertSame('rj', $params['user']);

                return true;
            }))
            ->willReturn('{"user": {}}');

        $client = new LastfmClient('my-api-key', httpClient: $httpClient);
        $client->call('user.getinfo', ['user' => 'rj']);
    }

    #[Test]
    public function itReturnsFullResponseArray(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn('{"user": {"name": "RJ"}, "extra": "data"}');

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->call('user.getinfo');

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('extra', $result);
        $this->assertSame('data', $result['extra']);
    }

    // ── callSigned() ───────────────────────────────────────────────

    #[Test]
    public function itCallsSignedWithSignatureButNoSessionKey(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $host = parse_url($url, PHP_URL_HOST);
                $this->assertSame('ws.audioscrobbler.com', $host);

                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string) $query, $params);
                $this->assertSame('auth.getsession', $params['method']);
                $this->assertSame('test-key', $params['api_key']);
                $this->assertArrayHasKey('api_sig', $params);
                $this->assertSame('json', $params['format']);
                $this->assertArrayNotHasKey('sk', $params);

                return true;
            }))
            ->willReturn('{"session": {}}');

        $client = new LastfmClient('test-key', 'test-secret', httpClient: $httpClient);

        $client->callSigned('auth.getsession', ['token' => 'tok']);
    }

    #[Test]
    public function itGeneratesCorrectSignatureForSignedCalls(): void
    {
        $capturedUrl = '';

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(
                function (string $url) use (&$capturedUrl): bool {
                    $capturedUrl = $url;
                    return true;
                },
            ))
            ->willReturn('{"result": {}}');

        $client = new LastfmClient('testkey', 'testsecret', httpClient: $httpClient);

        $client->callSigned('auth.getsession', ['token' => 'tok123']);

        $query = parse_url($capturedUrl, PHP_URL_QUERY);
        $this->assertIsString($query);
        parse_str((string) $query, $params);

        $expected = md5(
            'api_keytestkey'
            . 'methodauth.getsession'
            . 'tokentok123'
            . 'testsecret'
        );

        $this->assertSame($expected, $params['api_sig']);
    }

    #[Test]
    public function itThrowsWhenApiSecretMissingForSignedCalls(): void
    {
        $client = new LastfmClient('test-key');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API secret is required');

        $client->callSigned('auth.getsession');
    }

    // ── callAuthenticated() ────────────────────────────────────────

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

        $client = new LastfmClient('my-key', 'my-secret', 'my-session', $httpClient);

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

        $client = new LastfmClient('testkey', 'testsecret', 'testsk', $httpClient);

        $client->callAuthenticated('test.method', ['foo' => 'bar']);

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
        $client = new LastfmClient('test-api-key', 'secret');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Session key is required');

        $client->callAuthenticated('track.scrobble');
    }

    // ── setSessionKey() ────────────────────────────────────────────

    #[Test]
    public function itAllowsSettingSessionKeyAfterConstruction(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn('{"result": {}}');

        $client = new LastfmClient('key', 'secret', httpClient: $httpClient);

        // Should throw before setting session key
        try {
            $client->callAuthenticated('track.scrobble');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException) {
            // expected
        }

        // Should succeed after setting session key
        $client->setSessionKey('my-session');
        $result = $client->callAuthenticated('track.scrobble');
        $this->assertArrayHasKey('result', $result);
    }

    // ── Error handling ─────────────────────────────────────────────

    #[Test]
    public function itThrowsOnApiError(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn('{"error": 6, "message": "User not found"}');

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);

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

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);

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

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);

        $this->expectException(LastfmApiException::class);
        $this->expectExceptionMessage('Unknown API error');
        $this->expectExceptionCode(10);

        $client->call('user.getinfo');
    }

    #[Test]
    public function apiExceptionExposesErrorCode(): void
    {
        $exception = new LastfmApiException('Invalid API key', 10);

        $this->assertSame(10, $exception->getApiErrorCode());
        $this->assertSame('Invalid API key', $exception->getMessage());
    }

    #[Test]
    public function itHandlesApiErrorOnAuthenticatedPost(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn('{"error": 9, "message": "Invalid session key"}');

        $client = new LastfmClient('key', 'secret', 'bad-sk', $httpClient);

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

        $client = new LastfmClient('key', 'secret', 'sk', $httpClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to decode Last.fm API response');

        $client->callAuthenticated('track.scrobble');
    }
}
