<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagAlbumDto;

final class PersonalTagAlbumDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::albumData(), PersonalTagAlbumDto::class);

        $this->assertSame('OK Computer', $dto->name);
        $this->assertSame(
            'https://www.last.fm/music/Radiohead/OK+Computer',
            $dto->url,
        );
        $this->assertSame('album-mbid-789', $dto->mbid);
    }

    #[Test]
    public function itMapsArtistFields(): void
    {
        $dto = $this->mapper->map(self::albumData(), PersonalTagAlbumDto::class);

        $this->assertSame('Radiohead', $dto->artistName);
        $this->assertSame(
            'https://www.last.fm/music/Radiohead',
            $dto->artistUrl,
        );
        $this->assertSame('artist-mbid-123', $dto->artistMbid);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::albumData(), PersonalTagAlbumDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
        $this->assertSame(
            'https://lastfm.freetls.fastly.net/i/u/34s/img.png',
            $dto->images[0]->url,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function albumData(): array
    {
        return [
            'name' => 'OK Computer',
            'mbid' => 'album-mbid-789',
            'url' => 'https://www.last.fm/music/Radiohead/OK+Computer',
            'artist' => [
                'name' => 'Radiohead',
                'mbid' => 'artist-mbid-123',
                'url' => 'https://www.last.fm/music/Radiohead',
            ],
            'image' => [
                [
                    'size' => 'small',
                    '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png',
                ],
                [
                    'size' => 'large',
                    '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png',
                ],
            ],
        ];
    }
}
