<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Track\TrackAlbumDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackArtistDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackInfoDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackTagDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackWikiDto;

final class TrackInfoDtoTest extends TestCase
{
    #[Test]
    public function itHoldsTrackInfo(): void
    {
        $dto = new TrackInfoDto(
            name: 'One Step Closer',
            mbid: 'track-mbid-1',
            url: 'https://www.last.fm/music/Linkin+Park/_/One+Step+Closer',
            duration: 157000,
            streamable: false,
            fullTrackStreamable: false,
            listeners: 1315650,
            playcount: 11022708,
            artist: new TrackArtistDto('Linkin Park', 'artist-mbid-1', 'https://www.last.fm/music/Linkin+Park'),
            album: new TrackAlbumDto('Linkin Park', 'Album'),
            userPlaycount: 36,
            userLoved: false,
            topTags: [new TrackTagDto('rock', 'https://www.last.fm/tag/rock', 1)],
            wiki: new TrackWikiDto('published', 'summary', 'content'),
        );

        $this->assertSame('One Step Closer', $dto->name);
        $this->assertSame('Linkin Park', $dto->artist->name);
        $this->assertSame(36, $dto->userPlaycount);
        $this->assertCount(1, $dto->topTags);
    }
}
