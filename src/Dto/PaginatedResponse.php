<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

/**
 * A generic paginated response wrapper.
 *
 * @template T
 */
final readonly class PaginatedResponse
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        public array $items,
        public PaginationDto $pagination,
    ) {
    }
}
