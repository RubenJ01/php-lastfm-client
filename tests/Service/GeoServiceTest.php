<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Geo\GeoArtistDto;
use Rjds\PhpLastfmClient\Dto\Geo\GeoTrackDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class GeoServiceTest extends TestCase
{
    // ── getTopArtists ──────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->geo()->getTopArtists('germany');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(GeoArtistDto::class, $result->items[0]);
        $this->assertSame('Linkin Park', $result->items[0]->name);
        $this->assertSame(16232, $result->items[0]->listeners);
        $this->assertSame(1, $result->items[0]->rank);
        $this->assertSame('Rihanna', $result->items[1]->name);
    }

    #[Test]
    public function itReturnsPaginationForTopArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->geo()->getTopArtists('germany');

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(2, $result->pagination->perPage);
        $this->assertSame(2805, $result->pagination->total);
        $this->assertSame(1403, $result->pagination->totalPages);
    }

    #[Test]
    public function itCallsTopArtistsWithCorrectParameters(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('geo.gettopartists', $params['method']);
                $this->assertSame('germany', $params['country']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('3', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->geo()->getTopArtists('germany', 10, 3);
    }

    #[Test]
    public function itUsesDefaultLimitAndPageForTopArtists(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('50', $params['limit']);
                $this->assertSame('1', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->geo()->getTopArtists('germany');
    }

    #[Test]
    public function itParsesTopArtistImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->geo()->getTopArtists('germany');

        $this->assertCount(2, $result->items[0]->images);
        $this->assertInstanceOf(ImageDto::class, $result->items[0]->images[0]);
        $this->assertSame('small', $result->items[0]->images[0]->size);
    }

    // ── getTopTracks ───────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->geo()->getTopTracks('germany');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(GeoTrackDto::class, $result->items[0]);
        $this->assertSame('Stateside', $result->items[0]->name);
        $this->assertSame(7932, $result->items[0]->listeners);
        $this->assertSame(176, $result->items[0]->duration);
        $this->assertSame('PinkPantheress', $result->items[0]->artistName);
        $this->assertSame(0, $result->items[0]->rank);
        $this->assertSame('Babydoll', $result->items[1]->name);
    }

    #[Test]
    public function itReturnsPaginationForTopTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->geo()->getTopTracks('germany');

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(2, $result->pagination->perPage);
        $this->assertSame(10000, $result->pagination->total);
        $this->assertSame(5000, $result->pagination->totalPages);
    }

    #[Test]
    public function itCallsTopTracksWithCorrectParameters(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('geo.gettoptracks', $params['method']);
                $this->assertSame('france', $params['country']);
                $this->assertSame('5', $params['limit']);
                $this->assertSame('4', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->geo()->getTopTracks('france', 5, 4);
    }

    #[Test]
    public function itUsesDefaultLimitAndPageForTopTracks(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('50', $params['limit']);
                $this->assertSame('1', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->geo()->getTopTracks('germany');
    }

    #[Test]
    public function itParsesTopTrackImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->geo()->getTopTracks('germany');

        $this->assertCount(2, $result->items[0]->images);
        $this->assertInstanceOf(ImageDto::class, $result->items[0]->images[0]);
        $this->assertSame('small', $result->items[0]->images[0]->size);
    }

    // ── Fixtures ───────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private static function topArtistsResponse(): array
    {
        return [
            'topartists' => [
                'artist' => [
                    [
                        'name' => 'Linkin Park',
                        'listeners' => '16232',
                        'mbid' => 'f59c5520-5f46-4d2c-b2c4-822eabf53419',
                        'url' => 'https://www.last.fm/music/Linkin+Park',
                        'streamable' => '0',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                    [
                        'name' => 'Rihanna',
                        'listeners' => '15150',
                        'mbid' => '73e5e69d-3554-40d8-8516-00cb38737a1c',
                        'url' => 'https://www.last.fm/music/Rihanna',
                        'streamable' => '0',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '2'],
                    ],
                ],
                '@attr' => [
                    'country' => 'Germany',
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '1403',
                    'total' => '2805',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function topTracksResponse(): array
    {
        return [
            'tracks' => [
                'track' => [
                    [
                        'name' => 'Stateside',
                        'duration' => '176',
                        'listeners' => '7932',
                        'mbid' => 'ffbf7862-2476-4164-ac32-f5904ccefe0f',
                        'url' => 'https://www.last.fm/music/PinkPantheress/_/Stateside',
                        'streamable' => ['#text' => '0', 'fulltrack' => '0'],
                        'artist' => [
                            'name' => 'PinkPantheress',
                            'mbid' => '7441014f-f8f5-494f-81db-ff166fbc078d',
                            'url' => 'https://www.last.fm/music/PinkPantheress',
                        ],
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '0'],
                    ],
                    [
                        'name' => 'Babydoll',
                        'duration' => '97',
                        'listeners' => '5135',
                        'mbid' => 'c6fa2cd4-a100-4db8-8206-3cbcdd3aabe0',
                        'url' => 'https://www.last.fm/music/Dominic+Fike/_/Babydoll',
                        'streamable' => ['#text' => '0', 'fulltrack' => '0'],
                        'artist' => [
                            'name' => 'Dominic Fike',
                            'mbid' => 'e337c918-098f-418e-97a2-81dc224b1bf9',
                            'url' => 'https://www.last.fm/music/Dominic+Fike',
                        ],
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                ],
                '@attr' => [
                    'country' => 'Germany',
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '5000',
                    'total' => '10000',
                ],
            ],
        ];
    }

    /**
     * @return list<array{size: string, '#text': string}>
     */
    private static function imageData(): array
    {
        return [
            ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png'],
            ['size' => 'large', '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png'],
        ];
    }
}
