<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\SessionDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class AuthServiceTest extends TestCase
{
    #[Test]
    public function itGetsToken(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn('{"token": "abc123token"}');

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $token = $client->auth()->getToken();

        $this->assertSame('abc123token', $token);
    }

    #[Test]
    public function itCallsGetTokenWithCorrectMethod(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string) $query, $params);
                $this->assertSame('auth.gettoken', $params['method']);
                $this->assertSame('test-key', $params['api_key']);

                return true;
            }))
            ->willReturn('{"token": "abc123"}');

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $client->auth()->getToken();
    }

    #[Test]
    public function itBuildsAuthUrl(): void
    {
        $client = new LastfmClient('my-api-key');

        $url = $client->auth()->getAuthUrl('mytoken');

        $this->assertStringContainsString(
            'https://www.last.fm/api/auth/',
            $url,
        );

        $query = parse_url($url, PHP_URL_QUERY);
        $this->assertIsString($query);
        parse_str((string) $query, $params);
        $this->assertSame('my-api-key', $params['api_key']);
        $this->assertSame('mytoken', $params['token']);
        $this->assertArrayNotHasKey('cb', $params);
    }

    #[Test]
    public function itBuildsAuthUrlWithCallback(): void
    {
        $client = new LastfmClient('my-api-key');

        $url = $client->auth()->getAuthUrl('mytoken', 'https://example.com/cb');

        $query = parse_url($url, PHP_URL_QUERY);
        $this->assertIsString($query);
        parse_str((string) $query, $params);
        $this->assertSame('https://example.com/cb', $params['cb']);
    }

    #[Test]
    public function itGetsSession(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode([
                'session' => [
                    'name' => 'RubenJ01',
                    'key' => 'session-key-123',
                    'subscriber' => '0',
                ],
            ]));

        $client = new LastfmClient('test-key', 'test-secret', httpClient: $httpClient);

        $session = $client->auth()->getSession('authorized-token');

        $this->assertInstanceOf(SessionDto::class, $session);
        $this->assertSame('RubenJ01', $session->name);
        $this->assertSame('session-key-123', $session->key);
        $this->assertFalse($session->subscriber);
    }

    #[Test]
    public function itCallsGetSessionWithSignature(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string) $query, $params);
                $this->assertSame('auth.getsession', $params['method']);
                $this->assertSame('mytoken', $params['token']);
                $this->assertArrayHasKey('api_sig', $params);
                $this->assertSame('json', $params['format']);

                return true;
            }))
            ->willReturn((string) json_encode([
                'session' => [
                    'name' => 'RubenJ01',
                    'key' => 'sk',
                    'subscriber' => '0',
                ],
            ]));

        $client = new LastfmClient('test-key', 'test-secret', httpClient: $httpClient);

        $client->auth()->getSession('mytoken');
    }
}
