<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistCorrectionDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistSearchResultDto;
use Rjds\PhpLastfmClient\Dto\Artist\SimilarArtistDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackTagDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopAlbumDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTagDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTrackDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class ArtistServiceTest extends TestCase
{
    #[Test]
    public function itReturnsArtistInfo(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::artistInfoResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $dto = $client->artist()->getInfo('The Weeknd');

        $this->assertInstanceOf(ArtistDto::class, $dto);
        $this->assertSame('The Weeknd', $dto->name);
        $this->assertSame(1688480, $dto->stats->listeners);
        $this->assertNotNull($dto->bio);
        $this->assertCount(1, $dto->similarArtists);
        $this->assertSame('Drake', $dto->similarArtists[0]->name);
        $this->assertCount(1, $dto->tags);
        $this->assertSame('rnb', $dto->tags[0]->name);
    }

    #[Test]
    public function itPassesMbidAndOptionsToGetInfo(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('artist.getinfo', $params['method']);
                $this->assertSame('mbid-1', $params['mbid']);
                $this->assertSame('1', $params['autocorrect']);
                $this->assertSame('rj', $params['username']);
                $this->assertSame('de', $params['lang']);

                return true;
            }))
            ->willReturn((string) json_encode(self::artistInfoResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->artist()->getInfo(null, 'mbid-1', autocorrect: true, username: 'rj', lang: 'de');
    }

    #[Test]
    public function itReturnsArtistCorrection(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::correctionResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $dto = $client->artist()->getCorrection('Avici');

        $this->assertInstanceOf(ArtistCorrectionDto::class, $dto);
        $this->assertSame('Avicii', $dto->artist->name);
        $this->assertSame(0, $dto->index);
    }

    #[Test]
    public function itThrowsWhenCorrectionUnavailable(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(['corrections' => "\n                "]));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No artist correction available');

        $client->artist()->getCorrection('artistdoesntexist');
    }

    #[Test]
    public function itReturnsSimilarArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::similarArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $list = $client->artist()->getSimilar('Metallica');

        $this->assertCount(1, $list);
        $this->assertInstanceOf(SimilarArtistDto::class, $list[0]);
        $this->assertSame('Megadeth', $list[0]->name);
    }

    #[Test]
    public function itReturnsUserTagsForArtist(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::artistTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $tags = $client->artist()->getTags('rj', 'Metallica');

        $this->assertCount(1, $tags);
        $this->assertInstanceOf(TrackTagDto::class, $tags[0]);
        $this->assertSame('heavy metal', $tags[0]->name);
    }

    #[Test]
    public function itReturnsPaginatedTopAlbums(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topAlbumsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->artist()->getTopAlbums('Soundgarden');

        $this->assertCount(1, $result->items);
        $this->assertInstanceOf(UserTopAlbumDto::class, $result->items[0]);
        $this->assertSame('Superunknown', $result->items[0]->name);
    }

    #[Test]
    public function itReturnsTopTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $tags = $client->artist()->getTopTags('Metallica');

        $this->assertCount(1, $tags);
        $this->assertInstanceOf(UserTopTagDto::class, $tags[0]);
        $this->assertSame('metal', $tags[0]->name);
        $this->assertSame(100, $tags[0]->count);
    }

    #[Test]
    public function itReturnsPaginatedTopTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->artist()->getTopTracks('Metallica');

        $this->assertCount(1, $result->items);
        $this->assertInstanceOf(UserTopTrackDto::class, $result->items[0]);
        $this->assertSame('One', $result->items[0]->name);
    }

    #[Test]
    public function itSearchesArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::searchResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->artist()->search('Rammstein');

        $this->assertCount(1, $result->items);
        $this->assertInstanceOf(ArtistSearchResultDto::class, $result->items[0]);
        $this->assertSame('Rammstein', $result->items[0]->name);
        $this->assertSame(1913191, $result->items[0]->listeners);
    }

    #[Test]
    public function itRequiresArtistOrMbidWhenNotProvided(): void
    {
        $client = new LastfmClient('test-api-key');

        $this->expectException(\InvalidArgumentException::class);
        $client->artist()->getInfo();
    }

    /**
     * @return array<string, mixed>
     */
    private static function artistInfoResponse(): array
    {
        return [
            'artist' => [
                'name' => 'The Weeknd',
                'mbid' => 'c8b03190-306c-4120-bb0b-6f2ebfc06ea9',
                'url' => 'https://www.last.fm/music/The+Weeknd',
                'image' => [
                    ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png'],
                ],
                'streamable' => '0',
                'ontour' => '0',
                'stats' => [
                    'listeners' => '1688480',
                    'playcount' => '135692303',
                ],
                'similar' => [
                    'artist' => [
                        [
                            'name' => 'Drake',
                            'url' => 'https://www.last.fm/music/Drake',
                            'image' => [],
                        ],
                    ],
                ],
                'tags' => [
                    'tag' => [
                        ['name' => 'rnb', 'url' => 'https://www.last.fm/tag/rnb'],
                    ],
                ],
                'bio' => [
                    'published' => '09 Jan 2011',
                    'summary' => 'Summary text.',
                    'content' => 'Full bio.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function correctionResponse(): array
    {
        return [
            'corrections' => [
                'correction' => [
                    'artist' => [
                        'name' => 'Avicii',
                        'mbid' => 'c85cfd6b-b1e9-4a50-bd55-eb725f04f7d5',
                        'url' => 'https://www.last.fm/music/Avicii',
                    ],
                    '@attr' => ['index' => '0'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function similarArtistsResponse(): array
    {
        return [
            'similarartists' => [
                'artist' => [
                    [
                        'name' => 'Megadeth',
                        'mbid' => 'a9044915-8be3-4c7e-b11f-9e2d2ea0a91e',
                        'match' => '1',
                        'url' => 'https://www.last.fm/music/Megadeth',
                        'streamable' => '0',
                        'image' => [],
                    ],
                ],
                '@attr' => ['artist' => 'Metallica'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function artistTagsResponse(): array
    {
        return [
            'tags' => [
                'tag' => [
                    ['name' => 'heavy metal', 'url' => 'https://www.last.fm/tag/heavy+metal'],
                ],
                '@attr' => ['artist' => 'Metallica'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function topAlbumsResponse(): array
    {
        return [
            'topalbums' => [
                'album' => [
                    [
                        'name' => 'Superunknown',
                        'playcount' => 16024346,
                        'mbid' => '9d005b9c-fd45-412c-970b-3e64a59f84cd',
                        'url' => 'https://www.last.fm/music/Soundgarden/Superunknown',
                        'artist' => [
                            'name' => 'Soundgarden',
                            'mbid' => '153c9281-268f-4cf3-8938-f5a4593e5df4',
                            'url' => 'https://www.last.fm/music/Soundgarden',
                        ],
                        'image' => [],
                        '@attr' => ['rank' => '1'],
                    ],
                ],
                '@attr' => [
                    'artist' => 'Soundgarden',
                    'page' => '1',
                    'perPage' => '50',
                    'totalPages' => '10',
                    'total' => '100',
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
            'toptags' => [
                'tag' => [
                    [
                        'name' => 'metal',
                        'url' => 'https://www.last.fm/tag/metal',
                        'count' => '100',
                    ],
                ],
                '@attr' => ['artist' => 'Metallica'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function topTracksResponse(): array
    {
        return [
            'toptracks' => [
                'track' => [
                    [
                        'name' => 'One',
                        'playcount' => '999',
                        'mbid' => 'mbid-1',
                        'url' => 'https://www.last.fm/music/Metallica/_/One',
                        'artist' => [
                            'name' => 'Metallica',
                            'mbid' => 'mbid-a',
                            'url' => 'https://www.last.fm/music/Metallica',
                        ],
                        'image' => [],
                        '@attr' => ['rank' => '1'],
                    ],
                ],
                '@attr' => [
                    'artist' => 'Metallica',
                    'page' => '1',
                    'perPage' => '50',
                    'totalPages' => '5',
                    'total' => '50',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function searchResponse(): array
    {
        return [
            'results' => [
                'opensearch:Query' => [
                    '#text' => '',
                    'role' => 'request',
                    'searchTerms' => 'rammstein',
                    'startPage' => '1',
                ],
                'opensearch:totalResults' => '13809',
                'opensearch:startIndex' => '0',
                'opensearch:itemsPerPage' => '30',
                'artistmatches' => [
                    'artist' => [
                        [
                            'name' => 'Rammstein',
                            'listeners' => '1913191',
                            'mbid' => 'b2d122f9-eadb-4930-a196-8f221eeb0c66',
                            'url' => 'https://www.last.fm/music/Rammstein',
                            'streamable' => '0',
                            'image' => [],
                        ],
                    ],
                ],
                '@attr' => ['for' => 'rammstein'],
            ],
        ];
    }
}
