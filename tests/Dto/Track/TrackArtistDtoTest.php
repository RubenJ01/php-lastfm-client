<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Track\TrackArtistDto;

final class TrackArtistDtoTest extends TestCase
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
            'name' => 'Linkin Park',
            'mbid' => 'artist-mbid-1',
            'url' => 'https://www.last.fm/music/Linkin+Park',
        ], TrackArtistDto::class);

        $this->assertSame('Linkin Park', $dto->name);
        $this->assertSame('artist-mbid-1', $dto->mbid);
        $this->assertSame('https://www.last.fm/music/Linkin+Park', $dto->url);
    }
}
