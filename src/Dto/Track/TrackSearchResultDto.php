<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TrackSearchResultDto
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        #[MapFrom('artist')]
        public string $artistName,
        public string $url,
        #[CastTo('int')]
        public int $listeners,
        public string $mbid,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images = [],
    ) {
    }
}
