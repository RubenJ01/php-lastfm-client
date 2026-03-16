<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Geo;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class GeoTrackDto
{
    use HasToString;

    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        public string $url,
        public string $mbid,
        #[CastTo('int')]
        public int $duration,
        #[CastTo('int')]
        public int $listeners,
        #[MapFrom('artist.name')]
        public string $artistName,
        #[MapFrom('artist.url')]
        public string $artistUrl,
        #[MapFrom('artist.mbid')]
        public string $artistMbid,
        #[MapFrom('streamable.#text')]
        #[CastTo('bool')]
        public bool $streamable,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images,
        #[MapFrom('@attr.rank')]
        #[CastTo('int')]
        public int $rank,
    ) {
    }
}
