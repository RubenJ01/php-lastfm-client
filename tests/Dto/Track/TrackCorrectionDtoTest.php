<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Track\TrackCorrectionDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackCorrectionTrackDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackArtistDto;

final class TrackCorrectionDtoTest extends TestCase
{
    #[Test]
    public function itHoldsCorrection(): void
    {
        $dto = new TrackCorrectionDto(
            track: new TrackCorrectionTrackDto(
                name: 'I Wish',
                mbid: 'track-mbid-1',
                url: 'https://www.last.fm/music/Skee-Lo/_/I+Wish',
                artist: new TrackArtistDto('Skee-Lo'),
            ),
            artistCorrected: false,
            trackCorrected: false,
        );

        $this->assertSame('I Wish', $dto->track->name);
        $this->assertSame('Skee-Lo', $dto->track->artist?->name);
        $this->assertFalse($dto->artistCorrected);
    }
}
