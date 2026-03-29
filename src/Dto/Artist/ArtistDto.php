<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Artist;

use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ArtistDto
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<ImageDto>              $images
     * @param list<ArtistSummaryDto>      $similarArtists
     * @param list<ArtistTagDto>          $tags
     */
    public function __construct(
        public string $name,
        public string $mbid,
        public string $url,
        public bool $streamable,
        public bool $onTour,
        public ArtistStatsDto $stats,
        public ?ArtistBioDto $bio,
        public array $images,
        public array $similarArtists,
        public array $tags,
    ) {
    }
}
