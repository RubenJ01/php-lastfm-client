<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TrackAlbumDto
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $artist,
        public string $title,
        public ?string $mbid = null,
        public ?string $url = null,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images = [],
        #[MapFrom('@attr.position')]
        #[CastTo('int')]
        public ?int $position = null,
    ) {
    }
}
