<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Track\ScrobbleResponseDto;
use Rjds\PhpLastfmClient\Dto\Track\ScrobbleResultDto;

final class ScrobbleResponseDtoTest extends TestCase
{
    #[Test]
    public function itHoldsAcceptedIgnoredAndResults(): void
    {
        $result = new ScrobbleResultDto(
            track: 'Test',
            trackCorrected: false,
            artist: 'Artist',
            artistCorrected: false,
            album: '',
            albumCorrected: false,
            albumArtist: '',
            albumArtistCorrected: false,
            timestamp: 1287140447,
            ignoredCode: 0,
            ignoredMessage: '',
        );

        $response = new ScrobbleResponseDto(1, 0, [$result]);

        $this->assertSame(1, $response->accepted);
        $this->assertSame(0, $response->ignored);
        $this->assertCount(1, $response->scrobbles);
        $this->assertSame('Test', $response->scrobbles[0]->track);
    }
}
