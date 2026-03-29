<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopArtistDto;

final class TagTopArtistDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), TagTopArtistDto::class);

        $this->assertSame('System of a Down', $dto->name);
        $this->assertSame('https://www.last.fm/music/System+of+a+Down', $dto->url);
        $this->assertSame('mbid-1', $dto->mbid);
        $this->assertFalse($dto->streamable);
        $this->assertSame(0, $dto->playcount);
        $this->assertSame(2, $dto->rank);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), TagTopArtistDto::class);

        $this->assertCount(1, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
    }

    /**
     * @return array<string, mixed>
     */
    private static function artistApiData(): array
    {
        return [
            'name' => 'System of a Down',
            'mbid' => 'mbid-1',
            'url' => 'https://www.last.fm/music/System+of+a+Down',
            'streamable' => '0',
            'image' => [
                ['size' => 'medium', '#text' => 'https://lastfm.freetls.fastly.net/i/u/64s/img.png'],
            ],
            '@attr' => [
                'rank' => '2',
            ],
        ];
    }
}
