<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Exception\LastfmApiException;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;
use Rjds\PhpLastfmClient\Service\LibraryService;
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
}
