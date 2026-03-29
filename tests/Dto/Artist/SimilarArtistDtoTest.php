<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Artist\SimilarArtistDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final class SimilarArtistDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Megadeth',
            'mbid' => 'mbid-1',
            'match' => '0.75',
            'url' => 'https://www.last.fm/music/Megadeth',
            'streamable' => '0',
            'image' => [
                ['size' => 'small', '#text' => 'https://example.com/i.png'],
            ],
        ], SimilarArtistDto::class);

        $this->assertSame('Megadeth', $dto->name);
        $this->assertSame(0.75, $dto->match);
        $this->assertFalse($dto->streamable);
        $this->assertCount(1, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
    }
}
