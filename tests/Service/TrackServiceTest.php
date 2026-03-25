<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Track\Scrobble;
use Rjds\PhpLastfmClient\Dto\Track\ScrobbleResponseDto;
use Rjds\PhpLastfmClient\Dto\Track\SimilarTrackDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackCorrectionDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackInfoDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackSearchResultDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackTagDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class TrackServiceTest extends TestCase
{
    private function createAuthenticatedClient(
        HttpClientInterface $httpClient,
    ): LastfmClient {
        return new LastfmClient('test-key', 'test-secret', 'test-sk', $httpClient);
    }

    #[Test]
    public function itScrobblesSingleTrack(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn((string) json_encode(
                self::singleScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);
        $result = $client->track()->scrobble(new Scrobble(
            artist: 'Test Artist',
            track: 'Test Track',
            timestamp: 1287140447,
        ));

        $this->assertInstanceOf(ScrobbleResponseDto::class, $result);
        $this->assertSame(1, $result->accepted);
        $this->assertSame(0, $result->ignored);
        $this->assertCount(1, $result->scrobbles);
        $this->assertSame('Test Track', $result->scrobbles[0]->track);
        $this->assertSame('Test Artist', $result->scrobbles[0]->artist);
    }

    #[Test]
    public function itScrobblesBatchOfTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn((string) json_encode(
                self::batchScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);
        $result = $client->track()->scrobbleBatch([
            new Scrobble('Artist 0', 'Track 0', 1287141093),
            new Scrobble('Artist 1', 'Track 1', 1287141093),
        ]);

        $this->assertSame(2, $result->accepted);
        $this->assertSame(0, $result->ignored);
        $this->assertCount(2, $result->scrobbles);
        $this->assertSame('Track 0', $result->scrobbles[0]->track);
        $this->assertSame('Track 1', $result->scrobbles[1]->track);
    }

    #[Test]
    public function itSendsCorrectParametersForBatch(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->anything(),
                $this->callback(function (array $data): bool {
                    $this->assertSame('track.scrobble', $data['method']);
                    $this->assertSame('Artist A', $data['artist[0]']);
                    $this->assertSame('Track A', $data['track[0]']);
                    $this->assertSame('100', $data['timestamp[0]']);
                    $this->assertSame('Artist B', $data['artist[1]']);
                    $this->assertSame('Track B', $data['track[1]']);
                    $this->assertSame('200', $data['timestamp[1]']);

                    return true;
                }),
            )
            ->willReturn((string) json_encode(
                self::batchScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);
        $client->track()->scrobbleBatch([
            new Scrobble('Artist A', 'Track A', 100),
            new Scrobble('Artist B', 'Track B', 200),
        ]);
    }

    #[Test]
    public function itThrowsOnEmptyBatch(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = $this->createAuthenticatedClient($httpClient);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one scrobble is required');

        $client->track()->scrobbleBatch([]);
    }

    #[Test]
    public function itAllowsExactlyFiftyScrobbles(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn((string) json_encode(
                self::batchScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);

        $scrobbles = [];
        for ($i = 0; $i < 50; $i++) {
            $scrobbles[] = new Scrobble('Artist', 'Track', 100 + $i);
        }

        $result = $client->track()->scrobbleBatch($scrobbles);

        $this->assertInstanceOf(ScrobbleResponseDto::class, $result);
    }

    #[Test]
    public function itThrowsWhenBatchExceedsFifty(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = $this->createAuthenticatedClient($httpClient);

        $scrobbles = [];
        for ($i = 0; $i < 51; $i++) {
            $scrobbles[] = new Scrobble('Artist', 'Track', 100);
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('maximum of 50');

        $client->track()->scrobbleBatch($scrobbles);
    }

    #[Test]
    public function itReturnsTrackInfo(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::trackInfoResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $info = $client->track()->getInfo(artist: 'Linkin Park', track: 'One Step Closer');

        $this->assertInstanceOf(TrackInfoDto::class, $info);
        $this->assertSame('One Step Closer', $info->name);
        $this->assertSame('Linkin Park', $info->artist->name);
        $this->assertCount(5, $info->topTags);
    }

    #[Test]
    public function itCallsGetInfoWithArtistAndTrack(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('track.getinfo', $params['method']);
                $this->assertSame('Linkin Park', $params['artist']);
                $this->assertSame('One Step Closer', $params['track']);
                $this->assertSame('1', $params['autocorrect']);
                $this->assertSame('solelychloe', $params['username']);

                return true;
            }))
            ->willReturn((string) json_encode(self::trackInfoResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $client->track()->getInfo(
            artist: 'Linkin Park',
            track: 'One Step Closer',
            autocorrect: true,
            username: 'solelychloe',
        );
    }

    #[Test]
    public function itCallsGetInfoWithMbid(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('track.getinfo', $params['method']);
                $this->assertSame('30cb03f3-bd95-43b0-9d41-6d75e13cd353', $params['mbid']);
                $this->assertArrayNotHasKey('artist', $params);
                $this->assertArrayNotHasKey('track', $params);

                return true;
            }))
            ->willReturn((string) json_encode(self::trackInfoResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $client->track()->getInfo(mbid: '30cb03f3-bd95-43b0-9d41-6d75e13cd353');
    }

    #[Test]
    public function itThrowsIfGetInfoMissingRequiredIdentifiers(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = new LastfmClient('test-key', httpClient: $httpClient);

        $this->expectException(\InvalidArgumentException::class);
        $client->track()->getInfo();
    }

    #[Test]
    public function itReturnsSimilarTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::similarTracksResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $tracks = $client->track()->getSimilar(artist: 'Disturbed', track: 'Stricken');

        $this->assertCount(2, $tracks);
        $this->assertInstanceOf(SimilarTrackDto::class, $tracks[0]);
        $this->assertSame('Down With the Sickness', $tracks[0]->name);
    }

    #[Test]
    public function itCallsGetSimilarWithLimitAndAutocorrect(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('track.getsimilar', $params['method']);
                $this->assertSame('Disturbed', $params['artist']);
                $this->assertSame('Stricken', $params['track']);
                $this->assertSame('5', $params['limit']);
                $this->assertSame('1', $params['autocorrect']);

                return true;
            }))
            ->willReturn((string) json_encode(self::similarTracksResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $client->track()->getSimilar(artist: 'Disturbed', track: 'Stricken', limit: 5, autocorrect: true);
    }

    #[Test]
    public function itReturnsTopTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::topTagsResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $tags = $client->track()->getTopTags(artist: 'AC/DC', track: 'Hells Bells');

        $this->assertCount(2, $tags);
        $this->assertInstanceOf(TrackTagDto::class, $tags[0]);
        $this->assertSame('hard rock', $tags[0]->name);
        $this->assertSame(100, $tags[0]->count);
    }

    #[Test]
    public function itReturnsUserTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::tagsResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $tags = $client->track()->getTags(user: 'RJ', artist: 'AC/DC', track: 'Hells Bells');

        $this->assertCount(2, $tags);
        $this->assertSame('guitar', $tags[0]->name);
    }

    #[Test]
    public function itReturnsCorrection(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::correctionResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $correction = $client->track()->getCorrection('Skee-Lo', 'I wish');

        $this->assertInstanceOf(TrackCorrectionDto::class, $correction);
        $this->assertSame('I Wish', $correction->track->name);
    }

    #[Test]
    public function itReturnsSearchResults(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::searchResponse()));

        $client = new LastfmClient('test-key', httpClient: $httpClient);
        $result = $client->track()->search('Hells Bells', limit: 30, page: 1);

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(TrackSearchResultDto::class, $result->items[0]);
        $this->assertSame(11634, $result->pagination->total);
        $this->assertSame(30, $result->pagination->perPage);
        $this->assertSame(388, $result->pagination->totalPages);
    }

    /**
     * @return array<string, mixed>
     */
    private static function singleScrobbleResponse(): array
    {
        return [
            'scrobbles' => [
                'scrobble' => [
                    'track' => [
                        'corrected' => '0',
                        '#text' => 'Test Track',
                    ],
                    'artist' => [
                        'corrected' => '0',
                        '#text' => 'Test Artist',
                    ],
                    'album' => ['corrected' => '0', '#text' => ''],
                    'albumArtist' => [
                        'corrected' => '0',
                        '#text' => '',
                    ],
                    'timestamp' => '1287140447',
                    'ignoredMessage' => [
                        'code' => '0',
                        '#text' => '',
                    ],
                ],
                '@attr' => [
                    'accepted' => 1,
                    'ignored' => 0,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function batchScrobbleResponse(): array
    {
        return [
            'scrobbles' => [
                'scrobble' => [
                    self::scrobbleItem('Track 0', 'Artist 0'),
                    self::scrobbleItem('Track 1', 'Artist 1'),
                ],
                '@attr' => [
                    'accepted' => 2,
                    'ignored' => 0,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function trackInfoResponse(): array
    {
        return [
            'track' => [
                'name' => 'One Step Closer',
                'mbid' => '30cb03f3-bd95-43b0-9d41-6d75e13cd353',
                'url' => 'https://www.last.fm/music/Linkin+Park/_/One+Step+Closer',
                'duration' => '157000',
                'streamable' => ['#text' => '0', 'fulltrack' => '0'],
                'listeners' => '1315650',
                'playcount' => '11022708',
                'artist' => [
                    'name' => 'Linkin Park',
                    'mbid' => 'f59c5520-5f46-4d2c-b2c4-822eabf53419',
                    'url' => 'https://www.last.fm/music/Linkin+Park',
                ],
                'album' => [
                    'artist' => 'Linkin Park',
                    'title' => 'Road To Revolution: Live at Milton Keynes',
                    'mbid' => '9c7b6839-ce71-3741-a107-2f5dc678f4b4',
                    'url' => 'https://www.last.fm/music/Linkin+Park/Road+To+Revolution:+Live+at+Milton+Keynes',
                    'image' => [
                        ['#text' => 'https://lastfm/1.png', 'size' => 'small'],
                        ['#text' => 'https://lastfm/2.png', 'size' => 'large'],
                    ],
                    '@attr' => ['position' => '1'],
                ],
                'userplaycount' => '36',
                'userloved' => '0',
                'toptags' => [
                    'tag' => [
                        ['name' => 'Nu Metal', 'url' => 'https://www.last.fm/tag/Nu+Metal'],
                        ['name' => 'rock', 'url' => 'https://www.last.fm/tag/rock'],
                        ['name' => 'Linkin Park', 'url' => 'https://www.last.fm/tag/Linkin+Park'],
                        ['name' => 'alternative rock', 'url' => 'https://www.last.fm/tag/alternative+rock'],
                        ['name' => 'alternative', 'url' => 'https://www.last.fm/tag/alternative'],
                    ],
                ],
                'wiki' => [
                    'published' => '14 Apr 2009, 16:25',
                    'summary' => 'summary',
                    'content' => 'content',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function similarTracksResponse(): array
    {
        return [
            'similartracks' => [
                'track' => [
                    [
                        'name' => 'Down With the Sickness',
                        'playcount' => 8066450,
                        'mbid' => 'a8009036-b13e-4cb8-b2c7-2c3f9735b735',
                        'match' => 1,
                        'url' => 'https://www.last.fm/music/Disturbed/_/Down+With+the+Sickness',
                        'streamable' => ['#text' => '0', 'fulltrack' => '0'],
                        'duration' => 278,
                        'artist' => [
                            'name' => 'Disturbed',
                            'mbid' => 'dc75517c-d268-486d-bd5b-0eaff34eeef9',
                            'url' => 'https://www.last.fm/music/Disturbed',
                        ],
                        'image' => [
                            ['#text' => 'https://lastfm/1.png', 'size' => 'small'],
                            ['#text' => 'https://lastfm/2.png', 'size' => 'large'],
                        ],
                    ],
                    [
                        'name' => 'Inside the Fire',
                        'playcount' => 3983007,
                        'mbid' => '38c28d49-c0e4-4700-93d4-1c834400fe35',
                        'match' => 0.976467,
                        'url' => 'https://www.last.fm/music/Disturbed/_/Inside+the+Fire',
                        'streamable' => ['#text' => '0', 'fulltrack' => '0'],
                        'duration' => 233,
                        'artist' => [
                            'name' => 'Disturbed',
                            'mbid' => 'dc75517c-d268-486d-bd5b-0eaff34eeef9',
                            'url' => 'https://www.last.fm/music/Disturbed',
                        ],
                        'image' => [
                            ['#text' => 'https://lastfm/1.png', 'size' => 'small'],
                            ['#text' => 'https://lastfm/2.png', 'size' => 'large'],
                        ],
                    ],
                ],
                '@attr' => ['artist' => 'Disturbed'],
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
                    ['count' => 100, 'name' => 'hard rock', 'url' => 'https://www.last.fm/tag/hard+rock'],
                    ['count' => 58, 'name' => 'rock', 'url' => 'https://www.last.fm/tag/rock'],
                ],
                '@attr' => ['artist' => 'AC/DC', 'track' => 'Hells Bells'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function tagsResponse(): array
    {
        return [
            'tags' => [
                'tag' => [
                    ['name' => 'guitar', 'url' => 'https://www.last.fm/tag/guitar'],
                    ['name' => 'metal', 'url' => 'https://www.last.fm/tag/metal'],
                ],
                '@attr' => ['artist' => 'AC/DC', 'track' => 'Hells Bells'],
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
                    'track' => [
                        'name' => 'I Wish',
                        'mbid' => 'ccb9326a-6f9f-48b1-a097-1210dd14e119',
                        'url' => 'https://www.last.fm/music/Skee-Lo/_/I+Wish',
                        'artist' => [
                            'name' => 'Skee-Lo',
                            'mbid' => '9341a67c-4f0c-43c2-9ec4-c222d2cb97f3',
                            'url' => 'https://www.last.fm/music/Skee-Lo',
                        ],
                    ],
                    '@attr' => [
                        'index' => '0',
                        'artistcorrected' => '0',
                        'trackcorrected' => '0',
                    ],
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
                    'startPage' => '1',
                ],
                'opensearch:totalResults' => '11634',
                'opensearch:startIndex' => '0',
                'opensearch:itemsPerPage' => '30',
                'trackmatches' => [
                    'track' => [
                        [
                            'name' => 'Hells Bells',
                            'artist' => 'AC/DC',
                            'url' => 'https://www.last.fm/music/AC%2FDC/_/Hells+Bells',
                            'listeners' => '796758',
                            'image' => [
                                ['#text' => 'https://lastfm/1.png', 'size' => 'small'],
                                ['#text' => 'https://lastfm/2.png', 'size' => 'large'],
                            ],
                            'mbid' => 'b6411d6b-2dca-4004-8919-e8c27ff6b286',
                        ],
                        [
                            'name' => "Hell's Bells",
                            'artist' => 'Cary Ann Hearst',
                            'url' => 'https://www.last.fm/music/Cary+Ann+Hearst/_/Hell%27s+Bells',
                            'listeners' => '20485',
                            'image' => [
                                ['#text' => 'https://lastfm/1.png', 'size' => 'small'],
                                ['#text' => 'https://lastfm/2.png', 'size' => 'large'],
                            ],
                            'mbid' => 'd0fd3d3b-ae2f-40d5-a5ac-d56c0d549dc7',
                        ],
                    ],
                ],
                '@attr' => [],
            ],
        ];
    }
    /**
     * @return array<string, mixed>
     */
    private static function scrobbleItem(
        string $track,
        string $artist,
    ): array {
        return [
            'track' => ['corrected' => '0', '#text' => $track],
            'artist' => ['corrected' => '0', '#text' => $artist],
            'album' => ['corrected' => '0', '#text' => ''],
            'albumArtist' => ['corrected' => '0', '#text' => ''],
            'timestamp' => '1287141093',
            'ignoredMessage' => ['code' => '0', '#text' => ''],
        ];
    }
}
