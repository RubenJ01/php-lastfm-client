<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

use Rjds\PhpDto\Attribute\CastTo;

final readonly class PaginationDto
{
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
