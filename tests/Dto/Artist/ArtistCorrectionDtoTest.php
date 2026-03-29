<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistCorrectionArtistDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistCorrectionDto;

final class ArtistCorrectionDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsCorrectedArtist(): void
    {
        $artist = $this->mapper->map([
            'name' => 'Avicii',
            'mbid' => 'mbid-1',
            'url' => 'https://www.last.fm/music/Avicii',
        ], ArtistCorrectionArtistDto::class);

        $this->assertSame('Avicii', $artist->name);

        $dto = new ArtistCorrectionDto($artist, 2);

        $this->assertSame(2, $dto->index);
        $this->assertSame($artist, $dto->artist);
    }
}
