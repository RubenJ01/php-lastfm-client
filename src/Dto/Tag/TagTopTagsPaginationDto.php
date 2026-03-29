<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Tag;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TagTopTagsPaginationDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        #[CastTo('int')]
        public int $offset,
        #[MapFrom('num_res')]
        #[CastTo('int')]
        public int $numRes,
        #[CastTo('int')]
        public int $total,
    ) {
    }
}
