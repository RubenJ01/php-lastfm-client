<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Artist;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ArtistStatsDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        #[CastTo('int')]
        public int $listeners,
        #[CastTo('int')]
        public int $playcount,
        #[MapFrom('userplaycount')]
        #[CastTo('int')]
        public ?int $userPlaycount = null,
    ) {
    }
}
