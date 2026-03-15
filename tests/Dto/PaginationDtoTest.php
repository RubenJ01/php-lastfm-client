<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\PaginationDto;

final class PaginationDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromAttrData(): void
    {
        $dto = $this->mapper->map([
            'page' => '3',
            'perPage' => '50',
            'total' => '1931',
            'totalPages' => '39',
            'user' => 'rj',
        ], PaginationDto::class);

        $this->assertSame(3, $dto->page);
        $this->assertSame(50, $dto->perPage);
        $this->assertSame(1931, $dto->total);
        $this->assertSame(39, $dto->totalPages);
    }
}
