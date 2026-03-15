<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\LovedTrackDto;
use Rjds\PhpLastfmClient\Dto\User\UserDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class UserServiceTest extends TestCase
{
    #[Test]
    public function itReturnsUserDto(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::userGetInfoResponse()));

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
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('user.getinfo', $params['method']);
                $this->assertSame('testuser', $params['user']);

                return true;
            }))
            ->willReturn((string) json_encode(self::userGetInfoResponse('testuser')));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->user()->getInfo('testuser');
    }

    #[Test]
    public function itReturnsLovedTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn(
                (string) json_encode(self::lovedTracksResponse())
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
                (string) json_encode(self::lovedTracksResponse())
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
                parse_str((string) $query, $params);
                $this->assertSame('user.getlovedtracks', $params['method']);
                $this->assertSame('testuser', $params['user']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('3', $params['page']);

                return true;
            }))
            ->willReturn(
                (string) json_encode(self::lovedTracksResponse())
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
                parse_str((string) $query, $params);
                $this->assertSame('50', $params['limit']);
                $this->assertSame('1', $params['page']);

                return true;
            }))
            ->willReturn(
                (string) json_encode(self::lovedTracksResponse())
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
                (string) json_encode(self::lovedTracksResponse())
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
    private static function userGetInfoResponse(string $name = 'RJ'): array
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
}
