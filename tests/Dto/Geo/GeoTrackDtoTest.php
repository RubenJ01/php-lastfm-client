<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Geo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Geo\GeoTrackDto;

final class GeoTrackDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), GeoTrackDto::class);

        $this->assertSame('Stateside', $dto->name);
        $this->assertSame('https://www.last.fm/music/PinkPantheress/_/Stateside', $dto->url);
        $this->assertSame('ffbf7862-2476-4164-ac32-f5904ccefe0f', $dto->mbid);
        $this->assertSame(176, $dto->duration);
        $this->assertSame(7932, $dto->listeners);
        $this->assertFalse($dto->streamable);
        $this->assertSame(0, $dto->rank);
    }

    #[Test]
    public function itMapsArtistData(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), GeoTrackDto::class);

        $this->assertSame('PinkPantheress', $dto->artistName);
        $this->assertSame('https://www.last.fm/music/PinkPantheress', $dto->artistUrl);
        $this->assertSame('7441014f-f8f5-494f-81db-ff166fbc078d', $dto->artistMbid);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), GeoTrackDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
    }

    /**
     * @return array<string, mixed>
     */
    private static function trackApiData(): array
    {
        return [
            'name' => 'Stateside',
            'url' => 'https://www.last.fm/music/PinkPantheress/_/Stateside',
            'mbid' => 'ffbf7862-2476-4164-ac32-f5904ccefe0f',
            'duration' => '176',
            'listeners' => '7932',
            'streamable' => ['#text' => '0', 'fulltrack' => '0'],
            'artist' => [
                'name' => 'PinkPantheress',
                'mbid' => '7441014f-f8f5-494f-81db-ff166fbc078d',
                'url' => 'https://www.last.fm/music/PinkPantheress',
            ],
            'image' => [
                ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png'],
                ['size' => 'large', '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png'],
            ],
            '@attr' => ['rank' => '0'],
        ];
    }
}
