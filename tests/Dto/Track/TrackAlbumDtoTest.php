<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackAlbumDto;

final class TrackAlbumDtoTest extends TestCase
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
            'artist' => 'Linkin Park',
            'title' => 'Road To Revolution',
            'mbid' => 'album-mbid-1',
            'url' => 'https://www.last.fm/music/Linkin+Park/Road+To+Revolution',
            'image' => [
                ['#text' => 'https://lastfm/1.png', 'size' => 'small'],
                ['#text' => 'https://lastfm/2.png', 'size' => 'large'],
            ],
            '@attr' => ['position' => '1'],
        ], TrackAlbumDto::class);

        $this->assertSame('Linkin Park', $dto->artist);
        $this->assertSame('Road To Revolution', $dto->title);
        $this->assertSame('album-mbid-1', $dto->mbid);
        $this->assertSame('https://www.last.fm/music/Linkin+Park/Road+To+Revolution', $dto->url);
        $this->assertSame(1, $dto->position);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
    }
}
