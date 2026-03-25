<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopAlbumDto;

final class UserTopAlbumDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::albumApiData(), UserTopAlbumDto::class);

        $this->assertSame('OK Computer', $dto->name);
        $this->assertSame('https://www.last.fm/music/Radiohead/OK+Computer', $dto->url);
        $this->assertSame('album-mbid-1', $dto->mbid);
        $this->assertSame('Radiohead', $dto->artistName);
        $this->assertSame('artist-mbid-1', $dto->artistMbid);
        $this->assertSame('https://www.last.fm/music/Radiohead', $dto->artistUrl);
        $this->assertSame(50, $dto->playcount);
        $this->assertSame(1, $dto->rank);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::albumApiData(), UserTopAlbumDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
    }

    /**
     * @return array<string, mixed>
     */
    private static function albumApiData(): array
    {
        return [
            'name' => 'OK Computer',
            'mbid' => 'album-mbid-1',
            'url' => 'https://www.last.fm/music/Radiohead/OK+Computer',
            'playcount' => '50',
            'artist' => [
                'name' => 'Radiohead',
                'mbid' => 'artist-mbid-1',
                'url' => 'https://www.last.fm/music/Radiohead',
            ],
            'image' => [
                ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png'],
                ['size' => 'large', '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png'],
            ],
            '@attr' => [
                'rank' => '1',
            ],
        ];
    }
}
