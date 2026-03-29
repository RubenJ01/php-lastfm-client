<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistSearchResultDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final class ArtistSearchResultDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromSearchResponse(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Rammstein',
            'listeners' => '1913191',
            'mbid' => 'mbid-1',
            'url' => 'https://www.last.fm/music/Rammstein',
            'streamable' => '0',
            'image' => [],
        ], ArtistSearchResultDto::class);

        $this->assertSame('Rammstein', $dto->name);
        $this->assertSame(1913191, $dto->listeners);
        $this->assertFalse($dto->streamable);
        $this->assertSame([], $dto->images);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map([
            'name' => 'A',
            'listeners' => '1',
            'mbid' => 'm',
            'url' => 'u',
            'streamable' => '1',
            'image' => [
                ['size' => 'large', '#text' => 'https://example.com/x.png'],
            ],
        ], ArtistSearchResultDto::class);

        $this->assertCount(1, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
    }
}
