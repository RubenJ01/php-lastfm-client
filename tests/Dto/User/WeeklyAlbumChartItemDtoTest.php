<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyAlbumChartItemDto;

final class WeeklyAlbumChartItemDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::albumApiData(), WeeklyAlbumChartItemDto::class);

        $this->assertSame('OK Computer', $dto->name);
        $this->assertSame('https://www.last.fm/music/Radiohead/OK+Computer', $dto->url);
        $this->assertSame('album-mbid-1', $dto->mbid);
        $this->assertSame('Radiohead', $dto->artistName);
        $this->assertSame(10, $dto->playcount);
        $this->assertSame(1, $dto->rank);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::albumApiData(), WeeklyAlbumChartItemDto::class);

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
            'artist' => [
                '#text' => 'Radiohead',
                'mbid' => 'artist-mbid-1',
            ],
            'playcount' => '10',
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
