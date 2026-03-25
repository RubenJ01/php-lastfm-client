<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class WeeklyAlbumChartItemDto
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        public string $url,
        public string $mbid,
        #[MapFrom('artist.#text')]
        public string $artistName,
        #[CastTo('int')]
        public int $playcount,
        #[MapFrom('@attr.rank')]
        #[CastTo('int')]
        public int $rank,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images = [],
    ) {
    }
}
