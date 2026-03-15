<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final readonly class UserDto
{
    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        public string $realname,
        public string $url,
        public string $country,
        #[CastTo('int')]
        public int $age,
        #[CastTo('bool')]
        public bool $subscriber,
        #[CastTo('int')]
        public int $playcount,
        #[MapFrom('artist_count')]
        #[CastTo('int')]
        public int $artistCount,
        #[MapFrom('track_count')]
        #[CastTo('int')]
        public int $trackCount,
        #[MapFrom('album_count')]
        #[CastTo('int')]
        public int $albumCount,
        #[CastTo('int')]
        public int $playlists,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images,
        #[MapFrom('registered.unixtime')]
        #[CastTo('datetime')]
        public \DateTimeImmutable $registered,
        public string $type,
    ) {
    }
}
