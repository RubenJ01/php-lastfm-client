<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Track\SimilarTrackDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackArtistDto;

final class SimilarTrackDtoTest extends TestCase
{
    #[Test]
    public function itHoldsSimilarTrack(): void
    {
        $dto = new SimilarTrackDto(
            name: 'Down With the Sickness',
            playcount: 8066450,
            mbid: 'mbid-1',
            match: 1.0,
            url: 'https://www.last.fm/music/Disturbed/_/Down+With+the+Sickness',
            streamable: false,
            fullTrackStreamable: false,
            duration: 278,
            artist: new TrackArtistDto('Disturbed'),
            images: [new ImageDto('small', 'https://lastfm/1.png')],
        );

        $this->assertSame('Down With the Sickness', $dto->name);
        $this->assertSame('Disturbed', $dto->artist->name);
        $this->assertCount(1, $dto->images);
    }
}
