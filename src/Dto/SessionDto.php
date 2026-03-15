<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

use Rjds\PhpDto\Attribute\CastTo;

final readonly class SessionDto
{
    public function __construct(
        public string $name,
        public string $key,
        #[CastTo('bool')]
        public bool $subscriber,
    ) {
    }
}
