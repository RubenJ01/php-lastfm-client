<?php

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

class FriendDto
{
    use HasToString;

    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        public string $realname,
        public string $country,
        public string $url,
        #[CastTo('int')]
        public int $playlists,
        #[CastTo('int')]
        public int $playcount,
        #[CastTo('bool')]
        public bool $subscriber,
        #[MapFrom('image')]
        #[ArrayOf(ImageDto::class)]
        public array $images,
        #[MapFrom('registered.unixtime')]
        #[CastTo('datetime')]
        public \DateTimeImmutable $registered,
        public string $type,
        #[CastTo('bool')]
        public bool $bootstrap
    ) {
    }
}
