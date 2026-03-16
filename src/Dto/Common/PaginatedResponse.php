<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Common;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

/**
 * A generic paginated response wrapper.
 *
 * @template T
 */
final readonly class PaginatedResponse
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<T> $items
     */
    public function __construct(
        public array $items,
        public PaginationDto $pagination,
    ) {
    }
}
