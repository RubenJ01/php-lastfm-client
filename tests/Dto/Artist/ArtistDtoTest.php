<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistBioDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistStatsDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistSummaryDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistTagDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final class ArtistDtoTest extends TestCase
{
    #[Test]
    public function itHoldsAggregatedArtistData(): void
    {
        $stats = new ArtistStatsDto(10, 20, null);
        $bio = new ArtistBioDto('d', 's', 'c');
        $img = new ImageDto('small', 'https://x.com/a.png');
        $similar = [new ArtistSummaryDto('A', 'https://last.fm/a', [])];
        $tags = [new ArtistTagDto('t', 'https://last.fm/tag/t')];

        $dto = new ArtistDto(
            name: 'N',
            mbid: 'm',
            url: 'u',
            streamable: true,
            onTour: false,
            stats: $stats,
            bio: $bio,
            images: [$img],
            similarArtists: $similar,
            tags: $tags,
        );

        $this->assertSame('N', $dto->name);
        $this->assertSame(10, $dto->stats->listeners);
        $this->assertSame($bio, $dto->bio);
        $this->assertSame($img, $dto->images[0]);
        $this->assertSame('A', $dto->similarArtists[0]->name);
        $this->assertSame('t', $dto->tags[0]->name);
    }
}
