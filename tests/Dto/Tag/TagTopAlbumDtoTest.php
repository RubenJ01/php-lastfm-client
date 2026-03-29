<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopAlbumDto;

final class TagTopAlbumDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::albumApiData(), TagTopAlbumDto::class);

        $this->assertSame('Ten Thousand Fists', $dto->name);
        $this->assertSame('https://www.last.fm/music/Disturbed', $dto->url);
        $this->assertSame('album-mbid', $dto->mbid);
        $this->assertSame('Disturbed', $dto->artistName);
        $this->assertSame('artist-mbid', $dto->artistMbid);
        $this->assertSame('https://www.last.fm/music/Disturbed', $dto->artistUrl);
        $this->assertSame(0, $dto->playcount);
        $this->assertSame(1, $dto->rank);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::albumApiData(), TagTopAlbumDto::class);

        $this->assertCount(1, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
    }

    /**
     * @return array<string, mixed>
     */
    private static function albumApiData(): array
    {
        return [
            'name' => 'Ten Thousand Fists',
            'mbid' => 'album-mbid',
            'url' => 'https://www.last.fm/music/Disturbed',
            'artist' => [
                'name' => 'Disturbed',
                'mbid' => 'artist-mbid',
                'url' => 'https://www.last.fm/music/Disturbed',
            ],
            'image' => [
                ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png'],
            ],
            '@attr' => [
                'rank' => '1',
            ],
        ];
    }
}
