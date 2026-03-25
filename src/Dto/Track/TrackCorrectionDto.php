<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TrackCorrectionDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public TrackCorrectionTrackDto $track,
        #[MapFrom('@attr.artistcorrected')]
        #[CastTo('bool')]
        public bool $artistCorrected,
        #[MapFrom('@attr.trackcorrected')]
        #[CastTo('bool')]
        public bool $trackCorrected,
    ) {
    }
}
