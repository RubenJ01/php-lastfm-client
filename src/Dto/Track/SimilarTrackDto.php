<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class SimilarTrackDto
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        #[CastTo('int')]
        public int $playcount,
        public string $mbid,
        #[CastTo('float')]
        public float $match,
        public string $url,
        #[MapFrom('streamable.#text')]
        #[CastTo('bool')]
        public bool $streamable,
        #[MapFrom('streamable.fulltrack')]
        #[CastTo('bool')]
        public bool $fullTrackStreamable,
        #[CastTo('int')]
        public int $duration,
        public TrackArtistDto $artist,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images = [],
    ) {
    }
}
