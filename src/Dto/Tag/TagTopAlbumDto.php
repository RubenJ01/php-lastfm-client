<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Tag;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TagTopAlbumDto
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
        #[MapFrom('artist.name')]
        public string $artistName,
        #[MapFrom('artist.mbid')]
        public ?string $artistMbid = null,
        #[MapFrom('artist.url')]
        public ?string $artistUrl = null,
        #[CastTo('int')]
        public int $playcount = 0,
        #[MapFrom('@attr.rank')]
        #[CastTo('int')]
        public int $rank = 0,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images = [],
    ) {
    }
}
