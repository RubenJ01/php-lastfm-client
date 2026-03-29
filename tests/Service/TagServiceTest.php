<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Tag\TagGlobalTopTagDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagInfoDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagSimilarDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopAlbumDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopArtistDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTrackDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyChartRangeDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class TagServiceTest extends TestCase
{
    // ── getInfo ────────────────────────────────────────────────────

    #[Test]
    public function itReturnsTagInfo(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::tagInfoResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $info = $client->tag()->getInfo('metal');

        $this->assertInstanceOf(TagInfoDto::class, $info);
        $this->assertSame('metal', $info->name);
        $this->assertSame(1279720, $info->total);
        $this->assertSame(157277, $info->reach);
        $this->assertStringContainsString('Metal is a subgenre', $info->wiki->summary);
    }

    #[Test]
    public function itPassesLangAndTagToGetInfo(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('tag.getinfo', $params['method']);
                $this->assertSame('metal', $params['tag']);
                $this->assertSame('de', $params['lang']);

                return true;
            }))
            ->willReturn((string) json_encode(self::tagInfoResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->tag()->getInfo('metal', 'de');
    }

    #[Test]
    public function itNormalizesEmptyWikiForGetInfo(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode([
                'tag' => [
                    'name' => 'unknown',
                    'total' => '0',
                    'reach' => '0',
                    'wiki' => [],
                ],
            ]));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $info = $client->tag()->getInfo('unknown');

        $this->assertSame('', $info->wiki->summary);
        $this->assertSame('', $info->wiki->content);
    }

    // ── getSimilar ─────────────────────────────────────────────────

    #[Test]
    public function itReturnsSimilarTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::similarTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->tag()->getSimilar('metal');

        $this->assertSame('metal', $result->sourceTag);
        $this->assertCount(1, $result->tags);
        $this->assertInstanceOf(TagSimilarDto::class, $result->tags[0]);
        $this->assertSame('heavy metal', $result->tags[0]->name);
    }

    #[Test]
    public function itReturnsEmptySimilarTagsWhenApiReturnsEmptyArray(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode([
                'similartags' => [
                    'tag' => [],
                    '@attr' => ['tag' => 'n/a'],
                ],
            ]));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->tag()->getSimilar('metal');

        $this->assertNull($result->sourceTag);
        $this->assertCount(0, $result->tags);
    }

    // ── getTopAlbums ───────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopAlbums(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topAlbumsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->tag()->getTopAlbums('metal');

        $this->assertCount(1, $result->items);
        $this->assertInstanceOf(TagTopAlbumDto::class, $result->items[0]);
        $this->assertSame('Ten Thousand Fists', $result->items[0]->name);
        $this->assertSame('Disturbed', $result->items[0]->artistName);
        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(50, $result->pagination->perPage);
    }

    #[Test]
    public function itCallsTopAlbumsWithLimitAndPage(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('tag.gettopalbums', $params['method']);
                $this->assertSame('metal', $params['tag']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('2', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topAlbumsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->tag()->getTopAlbums('metal', 10, 2);
    }

    // ── getTopArtists ──────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->tag()->getTopArtists('metal');

        $this->assertCount(1, $result->items);
        $this->assertInstanceOf(TagTopArtistDto::class, $result->items[0]);
        $this->assertSame('System of a Down', $result->items[0]->name);
        $this->assertFalse($result->items[0]->streamable);
    }

    // ── getTopTags ───────────────────────────────────────────────────

    #[Test]
    public function itReturnsGlobalTopTagsWithOffsetPagination(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::globalTopTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->tag()->getTopTags();

        $this->assertCount(2, $result->tags);
        $this->assertInstanceOf(TagGlobalTopTagDto::class, $result->tags[0]);
        $this->assertSame('rock', $result->tags[0]->name);
        $this->assertSame(4024829, $result->tags[0]->count);
        $this->assertSame(0, $result->pagination->offset);
        $this->assertSame(50, $result->pagination->numRes);
        $this->assertSame(2804440, $result->pagination->total);
    }

    #[Test]
    public function itPassesLimitAndOffsetToGlobalTopTags(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('tag.gettoptags', $params['method']);
                $this->assertSame('25', $params['limit']);
                $this->assertSame('100', $params['offset']);

                return true;
            }))
            ->willReturn((string) json_encode(self::globalTopTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->tag()->getTopTags(25, 100);
    }

    // ── getTopTracks ───────────────────────────────────────────────

    #[Test]
    public function itReturnsPaginatedTopTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->tag()->getTopTracks('metal');

        $this->assertCount(1, $result->items);
        $this->assertInstanceOf(TagTopTrackDto::class, $result->items[0]);
        $this->assertSame('Chop Suey!', $result->items[0]->name);
        $this->assertSame(208, $result->items[0]->duration);
        $this->assertSame('System of a Down', $result->items[0]->artistName);
        $this->assertFalse($result->items[0]->streamable);
    }

    // ── getWeeklyChartList ─────────────────────────────────────────

    #[Test]
    public function itReturnsWeeklyChartRanges(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::weeklyChartListResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $ranges = $client->tag()->getWeeklyChartList('metal');

        $this->assertCount(1, $ranges);
        $this->assertInstanceOf(WeeklyChartRangeDto::class, $ranges[0]);
        $this->assertSame(1108296000, $ranges[0]->from);
        $this->assertSame(1108900800, $ranges[0]->to);
    }

    #[Test]
    public function itNormalizesSingleWeeklyChartEntry(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode([
                'weeklychartlist' => [
                    'chart' => [
                        '#text' => '',
                        'from' => '1108296000',
                        'to' => '1108900800',
                    ],
                    '@attr' => ['tag' => 'metal'],
                ],
            ]));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $ranges = $client->tag()->getWeeklyChartList('metal');

        $this->assertCount(1, $ranges);
        $this->assertSame(1108296000, $ranges[0]->from);
    }

    // ── Fixtures ───────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private static function tagInfoResponse(): array
    {
        return [
            'tag' => [
                'name' => 'metal',
                'total' => '1279720',
                'reach' => '157277',
                'wiki' => [
                    'summary' => 'Metal is a subgenre of rock music.',
                    'content' => 'Full wiki content.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function similarTagsResponse(): array
    {
        return [
            'similartags' => [
                'tag' => [
                    [
                        'name' => 'heavy metal',
                        'url' => 'https://www.last.fm/tag/heavy+metal',
                        'streamable' => '0',
                    ],
                ],
                '@attr' => [
                    'tag' => 'metal',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function topAlbumsResponse(): array
    {
        return [
            'albums' => [
                'album' => [
                    [
                        'name' => 'Ten Thousand Fists',
                        'mbid' => 'd618f88f-a4a7-4028-a9e7-a2f3bcc3d9c3',
                        'url' => 'https://www.last.fm/music/Disturbed',
                        'artist' => [
                            'name' => 'Disturbed',
                            'mbid' => 'dc75517c-d268-486d-bd5b-0eaff34eeef9',
                            'url' => 'https://www.last.fm/music/Disturbed',
                        ],
                        'image' => [],
                        '@attr' => ['rank' => '1'],
                    ],
                ],
                '@attr' => [
                    'tag' => 'metal',
                    'page' => '1',
                    'perPage' => '50',
                    'totalPages' => '585',
                    'total' => '29201',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function topArtistsResponse(): array
    {
        return [
            'topartists' => [
                'artist' => [
                    [
                        'name' => 'System of a Down',
                        'mbid' => 'cc0b7089-c08d-4c10-b6b0-873582c17fd6',
                        'url' => 'https://www.last.fm/music/System+of+a+Down',
                        'streamable' => '0',
                        'image' => [],
                        '@attr' => ['rank' => '1'],
                    ],
                ],
                '@attr' => [
                    'tag' => 'metal',
                    'page' => '1',
                    'perPage' => '50',
                    'totalPages' => '1192',
                    'total' => '59570',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function globalTopTagsResponse(): array
    {
        return [
            'toptags' => [
                'tag' => [
                    [
                        'name' => 'rock',
                        'count' => 4024829,
                        'reach' => 399440,
                    ],
                    [
                        'name' => 'electronic',
                        'count' => 2441085,
                        'reach' => 258464,
                    ],
                ],
                '@attr' => [
                    'offset' => 0,
                    'num_res' => 50,
                    'total' => 2804440,
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
                        'name' => 'Chop Suey!',
                        'duration' => '208',
                        'mbid' => 'd758947d-d667-430b-900d-2abb110f63aa',
                        'url' => 'https://www.last.fm/music/System+of+a+Down/_/Chop+Suey%21',
                        'streamable' => [
                            '#text' => '0',
                            'fulltrack' => '0',
                        ],
                        'artist' => [
                            'name' => 'System of a Down',
                            'mbid' => 'cc0b7089-c08d-4c10-b6b0-873582c17fd6',
                            'url' => 'https://www.last.fm/music/System+of+a+Down',
                        ],
                        'image' => [],
                        '@attr' => ['rank' => '1'],
                    ],
                ],
                '@attr' => [
                    'tag' => 'metal',
                    'page' => '1',
                    'perPage' => '50',
                    'totalPages' => '3316',
                    'total' => '165756',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function weeklyChartListResponse(): array
    {
        return [
            'weeklychartlist' => [
                'chart' => [
                    [
                        '#text' => '',
                        'from' => '1108296000',
                        'to' => '1108900800',
                    ],
                ],
                '@attr' => [
                    'tag' => 'metal',
                ],
            ],
        ];
    }
}
