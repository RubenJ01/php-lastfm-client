<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TrackTagDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $name,
        public string $url,
        #[CastTo('int')]
        public ?int $count = null,
    ) {
    }
}
