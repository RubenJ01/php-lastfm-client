<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Chart\ChartArtistDto;
use Rjds\PhpLastfmClient\Dto\Chart\ChartTagDto;
use Rjds\PhpLastfmClient\Dto\Chart\ChartTrackDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class ChartServiceTest extends TestCase
{
    // ── getTopArtists ──────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopArtists();

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(ChartArtistDto::class, $result->items[0]);
        $this->assertSame('PinkPantheress', $result->items[0]->name);
        $this->assertSame(308772499, $result->items[0]->playcount);
        $this->assertSame(2964326, $result->items[0]->listeners);
        $this->assertSame('The Weeknd', $result->items[1]->name);
    }

    #[Test]
    public function itReturnsPaginationForTopArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopArtists();

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(2, $result->pagination->perPage);
        $this->assertSame(10000, $result->pagination->total);
        $this->assertSame(5000, $result->pagination->totalPages);
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
                $this->assertSame('chart.gettopartists', $params['method']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('3', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->chart()->getTopArtists(10, 3);
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
        $client->chart()->getTopArtists();
    }

    #[Test]
    public function itParsesTopArtistImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopArtists();

        $this->assertCount(2, $result->items[0]->images);
        $this->assertInstanceOf(ImageDto::class, $result->items[0]->images[0]);
        $this->assertSame('small', $result->items[0]->images[0]->size);
    }

    // ── getTopTags ─────────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopTags();

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(ChartTagDto::class, $result->items[0]);
        $this->assertSame('rock', $result->items[0]->name);
        $this->assertSame(402881, $result->items[0]->reach);
        $this->assertSame(4069101, $result->items[0]->taggings);
        $this->assertTrue($result->items[0]->streamable);
        $this->assertSame('electronic', $result->items[1]->name);
    }

    #[Test]
    public function itReturnsPaginationForTopTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopTags();

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(2, $result->pagination->perPage);
    }

    #[Test]
    public function itCallsTopTagsWithCorrectParameters(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('chart.gettoptags', $params['method']);
                $this->assertSame('25', $params['limit']);
                $this->assertSame('2', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->chart()->getTopTags(25, 2);
    }

    #[Test]
    public function itUsesDefaultLimitAndPageForTopTags(): void
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
            ->willReturn((string) json_encode(self::topTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->chart()->getTopTags();
    }

    // ── getTopTracks ───────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopTracks();

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(ChartTrackDto::class, $result->items[0]);
        $this->assertSame('Stateside', $result->items[0]->name);
        $this->assertSame(13571831, $result->items[0]->playcount);
        $this->assertSame(958644, $result->items[0]->listeners);
        $this->assertSame(176, $result->items[0]->duration);
        $this->assertSame('PinkPantheress', $result->items[0]->artistName);
        $this->assertSame('American Girls', $result->items[1]->name);
    }

    #[Test]
    public function itReturnsPaginationForTopTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopTracks();

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
                $this->assertSame('chart.gettoptracks', $params['method']);
                $this->assertSame('5', $params['limit']);
                $this->assertSame('4', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->chart()->getTopTracks(5, 4);
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
        $client->chart()->getTopTracks();
    }

    #[Test]
    public function itParsesTopTrackImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->chart()->getTopTracks();

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
            'artists' => [
                'artist' => [
                    [
                        'name' => 'PinkPantheress',
                        'playcount' => '308772499',
                        'listeners' => '2964326',
                        'mbid' => '7441014f-f8f5-494f-81db-ff166fbc078d',
                        'url' => 'https://www.last.fm/music/PinkPantheress',
                        'streamable' => '0',
                        'image' => self::imageData(),
                    ],
                    [
                        'name' => 'The Weeknd',
                        'playcount' => '1100603355',
                        'listeners' => '5248986',
                        'mbid' => 'c8b03190-306c-4120-bb0b-6f2ebfc06ea9',
                        'url' => 'https://www.last.fm/music/The+Weeknd',
                        'streamable' => '0',
                        'image' => self::imageData(),
                    ],
                ],
                '@attr' => [
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '5000',
                    'total' => '10000',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function topTagsResponse(): array
    {
        return [
            'tags' => [
                'tag' => [
                    [
                        'name' => 'rock',
                        'url' => 'https://www.last.fm/tag/rock',
                        'reach' => '402881',
                        'taggings' => '4069101',
                        'streamable' => '1',
                        'wiki' => [],
                    ],
                    [
                        'name' => 'electronic',
                        'url' => 'https://www.last.fm/tag/electronic',
                        'reach' => '262194',
                        'taggings' => '2498526',
                        'streamable' => '1',
                        'wiki' => [],
                    ],
                ],
                '@attr' => [
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '1441026',
                    'total' => '2882052',
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
                        'playcount' => '13571831',
                        'listeners' => '958644',
                        'mbid' => 'ffbf7862-2476-4164-ac32-f5904ccefe0f',
                        'url' => 'https://www.last.fm/music/PinkPantheress/_/Stateside',
                        'streamable' => [
                            '#text' => '0',
                            'fulltrack' => '0',
                        ],
                        'artist' => [
                            'name' => 'PinkPantheress',
                            'mbid' => '7441014f-f8f5-494f-81db-ff166fbc078d',
                            'url' => 'https://www.last.fm/music/PinkPantheress',
                        ],
                        'image' => self::imageData(),
                    ],
                    [
                        'name' => 'American Girls',
                        'duration' => '213',
                        'playcount' => '1639064',
                        'listeners' => '341747',
                        'mbid' => '2c85fe70-3c0e-4b43-8d97-f5b5c4757f3a',
                        'url' => 'https://www.last.fm/music/Harry+Styles/_/American+Girls',
                        'streamable' => [
                            '#text' => '0',
                            'fulltrack' => '0',
                        ],
                        'artist' => [
                            'name' => 'Harry Styles',
                            'mbid' => '7eb1ce54-a355-41f9-8d68-e018b096d427',
                            'url' => 'https://www.last.fm/music/Harry+Styles',
                        ],
                        'image' => self::imageData(),
                    ],
                ],
                '@attr' => [
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
            [
                'size' => 'small',
                '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/2a96cbd8b46e442fc41c2b86b821562f.png',
            ],
            [
                'size' => 'large',
                '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/2a96cbd8b46e442fc41c2b86b821562f.png',
            ],
        ];
    }
}
