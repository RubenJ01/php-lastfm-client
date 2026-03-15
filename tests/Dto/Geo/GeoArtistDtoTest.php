<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Geo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Geo\GeoArtistDto;

final class GeoArtistDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), GeoArtistDto::class);

        $this->assertSame('Linkin Park', $dto->name);
        $this->assertSame('https://www.last.fm/music/Linkin+Park', $dto->url);
        $this->assertSame('f59c5520-5f46-4d2c-b2c4-822eabf53419', $dto->mbid);
        $this->assertSame(16232, $dto->listeners);
        $this->assertFalse($dto->streamable);
        $this->assertSame(1, $dto->rank);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), GeoArtistDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
    }

    /**
     * @return array<string, mixed>
     */
    private static function artistApiData(): array
    {
        return [
            'name' => 'Linkin Park',
            'url' => 'https://www.last.fm/music/Linkin+Park',
            'mbid' => 'f59c5520-5f46-4d2c-b2c4-822eabf53419',
            'listeners' => '16232',
            'streamable' => '0',
            'image' => [
                ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png'],
                ['size' => 'large', '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png'],
            ],
            '@attr' => ['rank' => '1'],
        ];
    }
}
