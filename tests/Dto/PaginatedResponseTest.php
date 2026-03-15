<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\PaginationDto;

final class PaginatedResponseTest extends TestCase
{
    #[Test]
    public function itHoldsItemsAndPagination(): void
    {
        $pagination = new PaginationDto(1, 50, 100, 2);
        $items = ['item1', 'item2'];

        $response = new PaginatedResponse($items, $pagination);

        $this->assertSame($items, $response->items);
        $this->assertSame(1, $response->pagination->page);
        $this->assertSame(50, $response->pagination->perPage);
        $this->assertSame(100, $response->pagination->total);
        $this->assertSame(2, $response->pagination->totalPages);
    }
}
