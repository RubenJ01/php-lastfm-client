<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Library\LibraryArtistDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class LibraryServiceTest extends TestCase
{
    #[Test]
    public function itReturnsPaginatedArtists(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::libraryGetArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->library()->getArtists('rj');

        $this->assertCount(2, $result->items);
        $this->assertInstanceOf(LibraryArtistDto::class, $result->items[0]);
        $this->assertSame('Queen', $result->items[0]->name);
        $this->assertSame(1511, $result->items[0]->playcount);
        $this->assertSame('Videoclub', $result->items[1]->name);
        $this->assertSame(1123, $result->items[1]->playcount);
    }

    #[Test]
    public function itReturnsPaginationMetadata(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::libraryGetArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->library()->getArtists('rj');

        $this->assertSame(1, $result->pagination->page);
        $this->assertSame(2, $result->pagination->perPage);
        $this->assertSame(1931, $result->pagination->total);
        $this->assertSame(966, $result->pagination->totalPages);
    }

    #[Test]
    public function itCallsApiWithCorrectParameters(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('library.getartists', $params['method']);
                $this->assertSame('testuser', $params['user']);
                $this->assertSame('10', $params['limit']);
                $this->assertSame('3', $params['page']);

                return true;
            }))
            ->willReturn((string) json_encode(self::libraryGetArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->library()->getArtists('testuser', 10, 3);
    }

    #[Test]
    public function itUsesDefaultLimitAndPage(): void
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
                (string) json_encode(self::libraryGetArtistsResponse())
            );

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $client->library()->getArtists('rj');
    }

    #[Test]
    public function itParsesArtistImages(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode(self::libraryGetArtistsResponse()));

        $client = new LastfmClient('test-api-key', httpClient: $httpClient);
        $result = $client->library()->getArtists('rj');

        $this->assertCount(2, $result->items[0]->images);
        $this->assertInstanceOf(ImageDto::class, $result->items[0]->images[0]);
        $this->assertSame('small', $result->items[0]->images[0]->size);
    }

    /**
     * @return array<string, mixed>
     */
    private static function libraryGetArtistsResponse(): array
    {
        return [
            'artists' => [
                'artist' => [
                    [
                        'name' => 'Queen',
                        'url' => 'https://www.last.fm/music/Queen',
                        'mbid' => '5eecaf18-02ec-47af-a4f2-7831db373419',
                        'tagcount' => '0',
                        'playcount' => '1511',
                        'streamable' => '0',
                        'image' => self::imageData(),
                    ],
                    [
                        'name' => 'Videoclub',
                        'url' => 'https://www.last.fm/music/Videoclub',
                        'mbid' => 'f4903035-e9cd-4b7f-b462-0447b0cd490a',
                        'tagcount' => '0',
                        'playcount' => '1123',
                        'streamable' => '0',
                        'image' => self::imageData(),
                    ],
                ],
                '@attr' => [
                    'page' => '1',
                    'total' => '1931',
                    'user' => 'rj',
                    'perPage' => '2',
                    'totalPages' => '966',
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
                '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png',
            ],
            [
                'size' => 'large',
                '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png',
            ],
        ];
    }
}
