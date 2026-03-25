<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TrackCorrectionTrackDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public ?string $name = null,
        public ?string $mbid = null,
        public ?string $url = null,
        public ?TrackArtistDto $artist = null,
    ) {
    }
}
