<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Tag;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TagSimilarDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $name,
        public string $url = '',
        #[CastTo('bool')]
        public bool $streamable = false,
        public ?string $match = null,
    ) {
    }
}
