<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;

final readonly class LibraryArtistDto
{
    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        public string $url,
        public string $mbid,
        #[CastTo('int')]
        public int $tagcount,
        #[CastTo('int')]
        public int $playcount,
        #[CastTo('bool')]
        public bool $streamable,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images,
    ) {
    }
}
