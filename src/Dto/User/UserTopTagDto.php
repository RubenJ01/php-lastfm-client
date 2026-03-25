<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class UserTopTagDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $name,
        public string $url,
        #[MapFrom('count')]
        #[CastTo('int')]
        public int $count,
    ) {
    }
}
