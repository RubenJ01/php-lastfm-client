<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Auth;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class SessionDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $name,
        public string $key,
        #[CastTo('bool')]
        public bool $subscriber,
    ) {
    }
}
