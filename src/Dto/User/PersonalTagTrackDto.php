<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class PersonalTagTrackDto
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
        public string $duration,
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
    ) {
    }
}
