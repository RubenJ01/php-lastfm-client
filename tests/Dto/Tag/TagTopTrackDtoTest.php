<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTrackDto;

final class TagTopTrackDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), TagTopTrackDto::class);

        $this->assertSame('Chop Suey!', $dto->name);
        $this->assertSame(208, $dto->duration);
        $this->assertSame('track-mbid', $dto->mbid);
        $this->assertSame('https://www.last.fm/music/SOAD/_/Chop', $dto->url);
        $this->assertSame('System of a Down', $dto->artistName);
        $this->assertSame('artist-mbid', $dto->artistMbid);
        $this->assertFalse($dto->streamable);
        $this->assertSame(0, $dto->playcount);
        $this->assertSame(0, $dto->listeners);
        $this->assertSame(1, $dto->rank);
    }

    #[Test]
    public function itParsesStreamableObjectAndImages(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), TagTopTrackDto::class);

        $this->assertCount(1, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
    }

    /**
     * @return array<string, mixed>
     */
    private static function trackApiData(): array
    {
        return [
            'name' => 'Chop Suey!',
            'duration' => '208',
            'mbid' => 'track-mbid',
            'url' => 'https://www.last.fm/music/SOAD/_/Chop',
            'streamable' => [
                '#text' => '0',
                'fulltrack' => '0',
            ],
            'artist' => [
                'name' => 'System of a Down',
                'mbid' => 'artist-mbid',
                'url' => 'https://www.last.fm/music/SOAD',
            ],
            'image' => [
                ['size' => 'large', '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png'],
            ],
            '@attr' => [
                'rank' => '1',
            ],
        ];
    }
}
