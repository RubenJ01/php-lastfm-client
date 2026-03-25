<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class RecentTrackDto
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
        #[MapFrom('artist.mbid')]
        public ?string $artistMbid = null,
        #[MapFrom('album.#text')]
        public ?string $albumName = null,
        #[MapFrom('date.uts')]
        #[CastTo('datetime')]
        public ?\DateTimeImmutable $scrobbledAt = null,
        #[MapFrom('@attr.nowplaying')]
        #[CastTo('bool')]
        public bool $nowPlaying = false,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images = [],
    ) {
    }
}
