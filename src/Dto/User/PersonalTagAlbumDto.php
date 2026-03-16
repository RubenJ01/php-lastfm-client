<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final readonly class PersonalTagAlbumDto
{
    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        public string $url,
        public string $mbid,
        #[MapFrom('artist.name')]
        public string $artistName,
        #[MapFrom('artist.url')]
        public string $artistUrl,
        #[MapFrom('artist.mbid')]
        public string $artistMbid,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images,
    ) {
    }
}
