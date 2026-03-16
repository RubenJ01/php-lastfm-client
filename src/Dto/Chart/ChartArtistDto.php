<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Chart;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ChartArtistDto
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
        public int $playcount,
        #[CastTo('int')]
        public int $listeners,
        #[CastTo('bool')]
        public bool $streamable,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images,
    ) {
    }
}
