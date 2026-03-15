<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Track\Scrobble;

final class ScrobbleTest extends TestCase
{
    #[Test]
    public function itConvertsRequiredFieldsToParams(): void
    {
        $scrobble = new Scrobble(
            artist: 'Radiohead',
            track: 'Karma Police',
            timestamp: 1287140447,
        );

        $params = $scrobble->toParams(0);

        $this->assertSame('Radiohead', $params['artist[0]']);
        $this->assertSame('Karma Police', $params['track[0]']);
        $this->assertSame('1287140447', $params['timestamp[0]']);
        $this->assertCount(3, $params);
    }

    #[Test]
    public function itConvertsAllOptionalFieldsToParams(): void
    {
        $scrobble = new Scrobble(
            artist: 'Radiohead',
            track: 'Karma Police',
            timestamp: 1287140447,
            album: 'OK Computer',
            albumArtist: 'Radiohead',
            trackNumber: 6,
            mbid: 'abc-123',
            duration: 264,
            chosenByUser: true,
        );

        $params = $scrobble->toParams(2);

        $this->assertSame('Radiohead', $params['artist[2]']);
        $this->assertSame('Karma Police', $params['track[2]']);
        $this->assertSame('1287140447', $params['timestamp[2]']);
        $this->assertSame('OK Computer', $params['album[2]']);
        $this->assertSame('Radiohead', $params['albumArtist[2]']);
        $this->assertSame('6', $params['trackNumber[2]']);
        $this->assertSame('abc-123', $params['mbid[2]']);
        $this->assertSame('264', $params['duration[2]']);
        $this->assertSame('1', $params['chosenByUser[2]']);
        $this->assertCount(9, $params);
    }

    #[Test]
    public function itOmitsNullOptionalFields(): void
    {
        $scrobble = new Scrobble(
            artist: 'Radiohead',
            track: 'Karma Police',
            timestamp: 1287140447,
            album: 'OK Computer',
        );

        $params = $scrobble->toParams(0);

        $this->assertCount(4, $params);
        $this->assertArrayNotHasKey('albumArtist[0]', $params);
        $this->assertArrayNotHasKey('trackNumber[0]', $params);
        $this->assertArrayNotHasKey('mbid[0]', $params);
        $this->assertArrayNotHasKey('duration[0]', $params);
        $this->assertArrayNotHasKey('chosenByUser[0]', $params);
    }

    #[Test]
    public function itConvertsChosenByUserFalseToZero(): void
    {
        $scrobble = new Scrobble(
            artist: 'Radiohead',
            track: 'Karma Police',
            timestamp: 1287140447,
            chosenByUser: false,
        );

        $params = $scrobble->toParams(0);

        $this->assertSame('0', $params['chosenByUser[0]']);
    }

    #[Test]
    public function itUsesCorrectBatchIndex(): void
    {
        $scrobble = new Scrobble(
            artist: 'Queen',
            track: 'Bohemian Rhapsody',
            timestamp: 1287140447,
        );

        $params = $scrobble->toParams(5);

        $this->assertArrayHasKey('artist[5]', $params);
        $this->assertArrayHasKey('track[5]', $params);
        $this->assertArrayHasKey('timestamp[5]', $params);
    }
}
