<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\FriendDto;
use Rjds\PhpLastfmClient\Dto\User\LovedTrackDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagAlbumDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagArtistDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagTrackDto;
use Rjds\PhpLastfmClient\Dto\User\RecentTrackDto;
use Rjds\PhpLastfmClient\Dto\User\UserDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopAlbumDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopArtistDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTagDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTrackDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyAlbumChartItemDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyArtistChartItemDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyChartRangeDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyTrackChartItemDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class UserServiceTest extends TestCase
{
    #[Test]
    public function itReturnsFriendDto(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string)json_encode(self::friendsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getFriends('aidan-');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(FriendDto::class, $result->items[0]);
        $this->assertSame('oldmaneatintwix', $result->items[0]->name);
        $this->assertSame('newmaneatintwix', $result->items[1]->name);
    }

    #[Test]
    public function itReturnsPaginationForFriends(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn(
                (string)json_encode(self::friendsResponse())
            );
        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getFriends('aidan-');

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(1, $result->pagination->perPage);
        $this->assertSame(10, $result->pagination->total);
        $this->assertSame(10, $result->pagination->totalPages);
    }

    #[Test]
    public function itCallsGetFriendsWithCorrectParams(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string)$query, $params);
                $this->assertSame('user.getfriends', $params['method']);
                $this->assertSame('aidan-', $params['user']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('1', $params['page']);

                return true;
            }))
            ->willReturn(
                (string)json_encode(self::friendsResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getFriends('aidan-', 10);
    }

    #[Test]
    public function itUsesDefaultLimitAndPageForFriends(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string)$query, $params);
                $this->assertSame('50', $params['limit']);
                $this->assertSame('1', $params['page']);

                return true;
            }))
            ->willReturn(
                (string)json_encode(self::friendsResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getFriends('aidan-');
    }

    #[Test]
    public function itParsesFriendsImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn(
                (string)json_encode(self::friendsResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getFriends('aidan-');

        $this->assertCount(2, $result->items[0]->images);
        $this->assertInstanceOf(
            ImageDto::class,
            $result->items[0]->images[0],
        );
        $this->assertSame('small', $result->items[0]->images[0]->size);
    }

    #[Test]
    public function itReturnsUserDto(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string)json_encode(self::getInfoResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $user = $client->user()->getInfo('rj');

        $this->assertInstanceOf(UserDto::class, $user);
        $this->assertSame('RJ', $user->name);
        $this->assertSame('Richard Jones', $user->realname);
        $this->assertSame(150316, $user->playcount);
        $this->assertTrue($user->subscriber);
    }

    #[Test]
    public function itCallsCorrectApiMethod(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string)parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('user.getinfo', $params['method']);
                $this->assertSame('testuser', $params['user']);

                return true;
            }))
            ->willReturn((string)json_encode(self::getInfoResponse('testuser')));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getInfo('testuser');
    }

    #[Test]
    public function itReturnsLovedTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn(
                (string)json_encode(self::lovedTracksResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getLovedTracks('rj');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(LovedTrackDto::class, $result->items[0]);
        $this->assertSame('Davy Crochet', $result->items[0]->name);
        $this->assertSame('The Backseat Lovers', $result->items[0]->artistName);
        $this->assertSame('Lucky', $result->items[1]->name);
        $this->assertSame('Radiohead', $result->items[1]->artistName);
    }

    #[Test]
    public function itReturnsPaginationForLovedTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn(
                (string)json_encode(self::lovedTracksResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getLovedTracks('rj');

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(2, $result->pagination->perPage);
        $this->assertSame(7790, $result->pagination->total);
        $this->assertSame(3895, $result->pagination->totalPages);
    }

    #[Test]
    public function itCallsGetLovedTracksWithCorrectParams(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string)$query, $params);
                $this->assertSame('user.getlovedtracks', $params['method']);
                $this->assertSame('testuser', $params['user']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('3', $params['page']);

                return true;
            }))
            ->willReturn(
                (string)json_encode(self::lovedTracksResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getLovedTracks('testuser', 10, 3);
    }

    #[Test]
    public function itUsesDefaultLimitAndPageForLovedTracks(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string)$query, $params);
                $this->assertSame('50', $params['limit']);
                $this->assertSame('1', $params['page']);

                return true;
            }))
            ->willReturn(
                (string)json_encode(self::lovedTracksResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getLovedTracks('rj');
    }

    #[Test]
    public function itParsesLovedTrackImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn(
                (string)json_encode(self::lovedTracksResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getLovedTracks('rj');

        $this->assertCount(2, $result->items[0]->images);
        $this->assertInstanceOf(
            ImageDto::class,
            $result->items[0]->images[0],
        );
        $this->assertSame('small', $result->items[0]->images[0]->size);
    }

    /**
     * @return array{user: array<string, mixed>}
     */
    private static function getInfoResponse(string $name = 'RJ'): array
    {
        return [
            'user' => [
                'name' => $name,
                'realname' => 'Richard Jones',
                'url' => "https://www.last.fm/user/{$name}",
                'country' => 'United Kingdom',
                'age' => '0',
                'subscriber' => '1',
                'playcount' => '150316',
                'artist_count' => '12749',
                'track_count' => '57066',
                'album_count' => '26658',
                'playlists' => '0',
                'image' => [
                    ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/image.png'],
                ],
                'registered' => ['unixtime' => '1037793040', '#text' => 1037793040],
                'type' => 'alum',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function lovedTracksResponse(): array
    {
        return [
            'lovedtracks' => [
                'track' => [
                    self::lovedTrackItem('Davy Crochet', 'The Backseat Lovers'),
                    self::lovedTrackItem('Lucky', 'Radiohead'),
                ],
                '@attr' => [
                    'page' => '1',
                    'total' => '7790',
                    'user' => 'rj',
                    'perPage' => '2',
                    'totalPages' => '3895',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function friendsResponse(): array
    {
        return [
            'friends' => [
                'user' => [
                    self::friendItem('oldmaneatintwix'),
                    self::friendItem('newmaneatintwix'),
                ],
                "@attr" => [
                    'page' => '1',
                    'total' => '10',
                    'user' => 'aidan-',
                    'perPage' => '1',
                    'totalPages' => '10'
                ]
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function friendItem(string $name): array
    {
        return [
            'playlists' => '0',
            'playcount' => '4',
            'subscriber' => '0',
            'name' => $name,
            'country' => 'United Kingdom',
            'image' => self::imageData(),
            'registered' => [
                'unixtime' => '160318968',
                '#text' => '2020-10-20 10:28',
            ],
            'url' => "https://www.last.fm/user/{$name}",
            'realname' => 'Charlie Brown',
            'bootstrap' => '0',
            'type' => 'user',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function lovedTrackItem(
        string $track,
        string $artist,
    ): array {
        return [
            'name' => $track,
            'url' => "https://www.last.fm/music/{$artist}/_/{$track}",
            'mbid' => 'track-mbid-123',
            'artist' => [
                'name' => $artist,
                'url' => "https://www.last.fm/music/{$artist}",
                'mbid' => 'artist-mbid-123',
            ],
            'date' => [
                'uts' => '1603112664',
                '#text' => '19 Oct 2020, 13:04',
            ],
            'image' => self::imageData(),
            'streamable' => [
                'fulltrack' => '0',
                '#text' => '0',
            ],
        ];
    }

    #[Test]
    public function itReturnsPersonalTagArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(
            (string)json_encode(self::personalTagsResponse('artist'))
        );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getPersonalTags('rj', 'rock', 'artist');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(PersonalTagArtistDto::class, $result->items[0]);
        $this->assertSame('Jack Bruce', $result->items[0]->name);
        $this->assertSame('Afghan Whigs', $result->items[1]->name);
    }

    #[Test]
    public function itReturnsPersonalTagTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(
            (string)json_encode(self::personalTagsResponse('track'))
        );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getPersonalTags('rj', 'rock', 'track');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(PersonalTagTrackDto::class, $result->items[0]);
        $this->assertSame('Arc Of A Diver', $result->items[0]->name);
        $this->assertSame('Steve Winwood', $result->items[0]->artistName);
    }

    #[Test]
    public function itReturnsPersonalTagAlbums(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(
            (string)json_encode(self::personalTagsResponse('album'))
        );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getPersonalTags('rj', 'rock', 'album');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(PersonalTagAlbumDto::class, $result->items[0]);
        $this->assertSame('OK Computer', $result->items[0]->name);
        $this->assertSame('Radiohead', $result->items[0]->artistName);
    }

    #[Test]
    public function itReturnsPaginationForPersonalTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(
            (string)json_encode(self::personalTagsResponse('artist'))
        );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getPersonalTags('rj', 'rock', 'artist');

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(2, $result->pagination->perPage);
        $this->assertSame(20, $result->pagination->total);
        $this->assertSame(10, $result->pagination->totalPages);
    }

    #[Test]
    public function itCallsGetPersonalTagsWithCorrectParams(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string)$query, $params);
                $this->assertSame('user.getpersonaltags', $params['method']);
                $this->assertSame('testuser', $params['user']);
                $this->assertSame('rock', $params['tag']);
                $this->assertSame('artist', $params['taggingtype']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('2', $params['page']);

                return true;
            }))
            ->willReturn(
                (string)json_encode(self::personalTagsResponse('artist'))
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getPersonalTags('testuser', 'rock', 'artist', 10, 2);
    }

    #[Test]
    public function itUsesDefaultLimitAndPageForPersonalTags(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string)$query, $params);
                $this->assertSame('50', $params['limit']);
                $this->assertSame('1', $params['page']);

                return true;
            }))
            ->willReturn(
                (string)json_encode(self::personalTagsResponse('artist'))
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getPersonalTags('rj', 'rock', 'artist');
    }

    #[Test]
    public function itParsesPersonalTagArtistImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(
            (string)json_encode(self::personalTagsResponse('artist'))
        );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getPersonalTags('rj', 'rock', 'artist');

        $this->assertCount(2, $result->items[0]->images);
        $this->assertInstanceOf(ImageDto::class, $result->items[0]->images[0]);
        $this->assertSame('small', $result->items[0]->images[0]->size);
    }

    #[Test]
    public function itThrowsOnInvalidTaggingType(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = new LastfmClient('test-api-key', httpClient: $httpClient);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Invalid tagging type 'invalid'. Must be one of: artist, album, track."
        );

        $client->user()->getPersonalTags('rj', 'rock', 'invalid');
    }

    /**
     * @return array<string, mixed>
     */
    private static function personalTagsResponse(string $type): array
    {
        $typeMap = [
            'artist' => [
                'plural' => 'artists',
                'singular' => 'artist',
                'items' => [
                    self::personalTagArtistItem('Jack Bruce'),
                    self::personalTagArtistItem('Afghan Whigs'),
                ],
            ],
            'track' => [
                'plural' => 'tracks',
                'singular' => 'track',
                'items' => [
                    self::personalTagTrackItem('Arc Of A Diver', 'Steve Winwood'),
                    self::personalTagTrackItem('Finish What Ya Started', 'Van Halen'),
                ],
            ],
            'album' => [
                'plural' => 'albums',
                'singular' => 'album',
                'items' => [
                    self::personalTagAlbumItem('OK Computer', 'Radiohead'),
                    self::personalTagAlbumItem('The Bends', 'Radiohead'),
                ],
            ],
        ];

        $config = $typeMap[$type];

        return [
            'taggings' => [
                $config['plural'] => [
                    $config['singular'] => $config['items'],
                ],
                '@attr' => [
                    'user' => 'RJ',
                    'tag' => 'rock',
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '10',
                    'total' => '20',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function personalTagArtistItem(string $name): array
    {
        return [
            'name' => $name,
            'mbid' => 'artist-mbid-123',
            'url' => "https://www.last.fm/music/" . urlencode($name),
            'streamable' => '0',
            'image' => self::imageData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function personalTagTrackItem(
        string $track,
        string $artist,
    ): array {
        return [
            'name' => $track,
            'duration' => 'FIXME',
            'mbid' => 'track-mbid-123',
            'url' => "https://www.last.fm/music/{$artist}/_/{$track}",
            'streamable' => ['#text' => '0', 'fulltrack' => '0'],
            'artist' => [
                'name' => $artist,
                'mbid' => 'artist-mbid-456',
                'url' => "https://www.last.fm/music/{$artist}",
            ],
            'image' => self::imageData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function personalTagAlbumItem(
        string $album,
        string $artist,
    ): array {
        return [
            'name' => $album,
            'mbid' => 'album-mbid-789',
            'url' => "https://www.last.fm/music/{$artist}/{$album}",
            'artist' => [
                'name' => $artist,
                'mbid' => 'artist-mbid-123',
                'url' => "https://www.last.fm/music/{$artist}",
            ],
            'image' => self::imageData(),
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
                '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png',
            ],
            [
                'size' => 'large',
                '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png',
            ],
        ];
    }

    #[Test]
    public function itReturnsRecentTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(
            (string) json_encode(self::recentTracksResponse())
        );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getRecentTracks('rj');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(RecentTrackDto::class, $result->items[0]);
        $this->assertSame('Karma Police', $result->items[0]->name);
        $this->assertSame('Radiohead', $result->items[0]->artistName);
        $this->assertFalse($result->items[0]->nowPlaying);

        $this->assertTrue($result->items[1]->nowPlaying);
        $this->assertNull($result->items[1]->scrobbledAt);
    }

    #[Test]
    public function itCallsGetRecentTracksWithCorrectParams(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string) $query, $params);
                $this->assertSame('user.getrecenttracks', $params['method']);
                $this->assertSame('testuser', $params['user']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('3', $params['page']);
                $this->assertSame('100', $params['from']);
                $this->assertSame('200', $params['to']);
                $this->assertSame('1', $params['extended']);

                return true;
            }))
            ->willReturn((string) json_encode(self::recentTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getRecentTracks('testuser', limit: 10, page: 3, from: 100, to: 200, extended: true);
    }

    #[Test]
    public function itReturnsTopArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getTopArtists('rj');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(UserTopArtistDto::class, $result->items[0]);
        $this->assertSame('Radiohead', $result->items[0]->name);
        $this->assertSame(1, $result->items[0]->rank);
    }

    #[Test]
    public function itCallsGetTopArtistsWithCorrectParams(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);
                $this->assertIsString($query);
                parse_str((string) $query, $params);
                $this->assertSame('user.gettopartists', $params['method']);
                $this->assertSame('testuser', $params['user']);
                $this->assertSame('7day', $params['period']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('2', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::topArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getTopArtists('testuser', period: '7day', limit: 10, page: 2);
    }

    #[Test]
    public function itReturnsTopAlbums(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::topAlbumsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getTopAlbums('rj');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(UserTopAlbumDto::class, $result->items[0]);
        $this->assertSame('OK Computer', $result->items[0]->name);
        $this->assertSame('Radiohead', $result->items[0]->artistName);
    }

    #[Test]
    public function itReturnsTopTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::topTracksResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getTopTracks('rj');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(UserTopTrackDto::class, $result->items[0]);
        $this->assertSame('Karma Police', $result->items[0]->name);
        $this->assertSame('Radiohead', $result->items[0]->artistName);
    }

    #[Test]
    public function itReturnsTopTags(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::topTagsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->user()->getTopTags('rj');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(UserTopTagDto::class, $result->items[0]);
        $this->assertSame('rock', $result->items[0]->name);
        $this->assertSame(100, $result->items[0]->count);
    }

    #[Test]
    public function itReturnsWeeklyChartList(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::weeklyChartListResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $ranges = $client->user()->getWeeklyChartList('rj');

        $this->assertCount(2, $ranges);
        $this->assertInstanceOf(WeeklyChartRangeDto::class, $ranges[0]);
        $this->assertSame(100, $ranges[0]->from);
        $this->assertSame(200, $ranges[0]->to);
    }

    #[Test]
    public function itReturnsWeeklyArtistChart(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::weeklyArtistChartResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $items = $client->user()->getWeeklyArtistChart('rj');

        $this->assertCount(2, $items);
        $this->assertInstanceOf(WeeklyArtistChartItemDto::class, $items[0]);
        $this->assertSame('Radiohead', $items[0]->name);
    }

    #[Test]
    public function itReturnsWeeklyAlbumChart(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::weeklyAlbumChartResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $items = $client->user()->getWeeklyAlbumChart('rj');

        $this->assertCount(2, $items);
        $this->assertInstanceOf(WeeklyAlbumChartItemDto::class, $items[0]);
        $this->assertSame('OK Computer', $items[0]->name);
        $this->assertSame('Radiohead', $items[0]->artistName);
    }

    #[Test]
    public function itReturnsWeeklyTrackChart(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')->willReturn((string) json_encode(self::weeklyTrackChartResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $items = $client->user()->getWeeklyTrackChart('rj');

        $this->assertCount(2, $items);
        $this->assertInstanceOf(WeeklyTrackChartItemDto::class, $items[0]);
        $this->assertSame('Karma Police', $items[0]->name);
        $this->assertSame('Radiohead', $items[0]->artistName);
    }

    /**
     * @return array<string, mixed>
     */
    private static function recentTracksResponse(): array
    {
        return [
            'recenttracks' => [
                'track' => [
                    [
                        'name' => 'Karma Police',
                        'mbid' => 'track-mbid-1',
                        'url' => 'https://www.last.fm/music/Radiohead/_/Karma+Police',
                        'artist' => [
                            '#text' => 'Radiohead',
                            'mbid' => 'artist-mbid-1',
                        ],
                        'album' => [
                            '#text' => 'OK Computer',
                        ],
                        'image' => self::imageData(),
                        'date' => [
                            'uts' => '1603112664',
                            '#text' => '19 Oct 2020, 13:04',
                        ],
                    ],
                    [
                        'name' => 'Paranoid Android',
                        'mbid' => 'track-mbid-2',
                        'url' => 'https://www.last.fm/music/Radiohead/_/Paranoid+Android',
                        'artist' => [
                            '#text' => 'Radiohead',
                            'mbid' => 'artist-mbid-1',
                        ],
                        'album' => [
                            '#text' => 'OK Computer',
                        ],
                        'image' => self::imageData(),
                        '@attr' => [
                            'nowplaying' => 'true',
                        ],
                    ],
                ],
                '@attr' => [
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '10',
                    'total' => '20',
                    'user' => 'rj',
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
                        'name' => 'Radiohead',
                        'mbid' => 'artist-mbid-1',
                        'url' => 'https://www.last.fm/music/Radiohead',
                        'playcount' => '100',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                    [
                        'name' => 'The National',
                        'mbid' => 'artist-mbid-2',
                        'url' => 'https://www.last.fm/music/The+National',
                        'playcount' => '90',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '2'],
                    ],
                ],
                '@attr' => [
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '10',
                    'total' => '20',
                    'user' => 'rj',
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
            'topalbums' => [
                'album' => [
                    [
                        'name' => 'OK Computer',
                        'mbid' => 'album-mbid-1',
                        'url' => 'https://www.last.fm/music/Radiohead/OK+Computer',
                        'playcount' => '50',
                        'artist' => [
                            'name' => 'Radiohead',
                            'mbid' => 'artist-mbid-1',
                            'url' => 'https://www.last.fm/music/Radiohead',
                        ],
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                    [
                        'name' => 'In Rainbows',
                        'mbid' => 'album-mbid-2',
                        'url' => 'https://www.last.fm/music/Radiohead/In+Rainbows',
                        'playcount' => '40',
                        'artist' => [
                            'name' => 'Radiohead',
                            'mbid' => 'artist-mbid-1',
                            'url' => 'https://www.last.fm/music/Radiohead',
                        ],
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '2'],
                    ],
                ],
                '@attr' => [
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '10',
                    'total' => '20',
                    'user' => 'rj',
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
            'toptracks' => [
                'track' => [
                    [
                        'name' => 'Karma Police',
                        'mbid' => 'track-mbid-1',
                        'url' => 'https://www.last.fm/music/Radiohead/_/Karma+Police',
                        'playcount' => '25',
                        'artist' => [
                            'name' => 'Radiohead',
                            'mbid' => 'artist-mbid-1',
                            'url' => 'https://www.last.fm/music/Radiohead',
                        ],
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                    [
                        'name' => 'Paranoid Android',
                        'mbid' => 'track-mbid-2',
                        'url' => 'https://www.last.fm/music/Radiohead/_/Paranoid+Android',
                        'playcount' => '24',
                        'artist' => [
                            'name' => 'Radiohead',
                            'mbid' => 'artist-mbid-1',
                            'url' => 'https://www.last.fm/music/Radiohead',
                        ],
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '2'],
                    ],
                ],
                '@attr' => [
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '10',
                    'total' => '20',
                    'user' => 'rj',
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
                    ['name' => 'rock', 'url' => 'https://www.last.fm/tag/rock', 'count' => '100'],
                    ['name' => 'alternative', 'url' => 'https://www.last.fm/tag/alternative', 'count' => '90'],
                ],
                '@attr' => [
                    'page' => '1',
                    'perPage' => '2',
                    'totalPages' => '10',
                    'total' => '20',
                    'user' => 'rj',
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
                    ['from' => '100', 'to' => '200'],
                    ['from' => '200', 'to' => '300'],
                ],
                '@attr' => [
                    'user' => 'rj',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function weeklyArtistChartResponse(): array
    {
        return [
            'weeklyartistchart' => [
                'artist' => [
                    [
                        'name' => 'Radiohead',
                        'mbid' => 'artist-mbid-1',
                        'url' => 'https://www.last.fm/music/Radiohead',
                        'playcount' => '10',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                    [
                        'name' => 'The National',
                        'mbid' => 'artist-mbid-2',
                        'url' => 'https://www.last.fm/music/The+National',
                        'playcount' => '9',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '2'],
                    ],
                ],
                '@attr' => [
                    'user' => 'rj',
                    'from' => '100',
                    'to' => '200',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function weeklyAlbumChartResponse(): array
    {
        return [
            'weeklyalbumchart' => [
                'album' => [
                    [
                        'name' => 'OK Computer',
                        'mbid' => 'album-mbid-1',
                        'url' => 'https://www.last.fm/music/Radiohead/OK+Computer',
                        'artist' => ['#text' => 'Radiohead', 'mbid' => 'artist-mbid-1'],
                        'playcount' => '10',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                    [
                        'name' => 'In Rainbows',
                        'mbid' => 'album-mbid-2',
                        'url' => 'https://www.last.fm/music/Radiohead/In+Rainbows',
                        'artist' => ['#text' => 'Radiohead', 'mbid' => 'artist-mbid-1'],
                        'playcount' => '9',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '2'],
                    ],
                ],
                '@attr' => [
                    'user' => 'rj',
                    'from' => '100',
                    'to' => '200',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function weeklyTrackChartResponse(): array
    {
        return [
            'weeklytrackchart' => [
                'track' => [
                    [
                        'name' => 'Karma Police',
                        'mbid' => 'track-mbid-1',
                        'url' => 'https://www.last.fm/music/Radiohead/_/Karma+Police',
                        'artist' => ['#text' => 'Radiohead', 'mbid' => 'artist-mbid-1'],
                        'playcount' => '10',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '1'],
                    ],
                    [
                        'name' => 'Paranoid Android',
                        'mbid' => 'track-mbid-2',
                        'url' => 'https://www.last.fm/music/Radiohead/_/Paranoid+Android',
                        'artist' => ['#text' => 'Radiohead', 'mbid' => 'artist-mbid-1'],
                        'playcount' => '9',
                        'image' => self::imageData(),
                        '@attr' => ['rank' => '2'],
                    ],
                ],
                '@attr' => [
                    'user' => 'rj',
                    'from' => '100',
                    'to' => '200',
                ],
            ],
        ];
    }
}
