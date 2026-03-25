<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Track\TrackArtistDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackCorrectionTrackDto;

final class TrackCorrectionTrackDtoTest extends TestCase
{
    #[Test]
    public function itHoldsCorrectedTrack(): void
    {
        $dto = new TrackCorrectionTrackDto(
            name: 'I Wish',
            mbid: 'track-mbid-1',
            url: 'https://www.last.fm/music/Skee-Lo/_/I+Wish',
            artist: new TrackArtistDto('Skee-Lo'),
        );

        $this->assertSame('I Wish', $dto->name);
        $this->assertSame('Skee-Lo', $dto->artist?->name);
    }
}
