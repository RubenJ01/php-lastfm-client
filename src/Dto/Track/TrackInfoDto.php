<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TrackInfoDto
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<TrackTagDto> $topTags
     */
    public function __construct(
        public string $name,
        public ?string $mbid,
        public string $url,
        #[CastTo('int')]
        public int $duration,
        #[MapFrom('streamable.#text')]
        #[CastTo('bool')]
        public bool $streamable,
        #[MapFrom('streamable.fulltrack')]
        #[CastTo('bool')]
        public bool $fullTrackStreamable,
        #[CastTo('int')]
        public int $listeners,
        #[CastTo('int')]
        public int $playcount,
        public TrackArtistDto $artist,
        public ?TrackAlbumDto $album = null,
        #[MapFrom('userplaycount')]
        #[CastTo('int')]
        public ?int $userPlaycount = null,
        #[MapFrom('userloved')]
        #[CastTo('bool')]
        public ?bool $userLoved = null,
        #[MapFrom('toptags.tag')]
        #[ArrayOf(TrackTagDto::class)]
        public array $topTags = [],
        public ?TrackWikiDto $wiki = null,
    ) {
    }
}
