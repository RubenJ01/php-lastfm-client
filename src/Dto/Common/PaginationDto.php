<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Common;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class PaginationDto
{
    use HasToString;

    public function __construct(
        #[CastTo('int')]
        public int $page,
        #[CastTo('int')]
        public int $perPage,
        #[CastTo('int')]
        public int $total,
        #[CastTo('int')]
        public int $totalPages,
    ) {
    }
}
