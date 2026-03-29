<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Tag;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TagInfoDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $name,
        #[CastTo('int')]
        public int $total,
        #[CastTo('int')]
        public int $reach,
        public TagWikiDto $wiki,
    ) {
    }
}
