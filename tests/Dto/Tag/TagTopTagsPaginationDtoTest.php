<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTagsPaginationDto;

final class TagTopTagsPaginationDtoTest extends TestCase
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
            'offset' => 0,
            'num_res' => 50,
            'total' => 2804440,
        ], TagTopTagsPaginationDto::class);

        $this->assertSame(0, $dto->offset);
        $this->assertSame(50, $dto->numRes);
        $this->assertSame(2804440, $dto->total);
    }

    #[Test]
    public function itMapsStringValuesFromApi(): void
    {
        $dto = $this->mapper->map([
            'offset' => '100',
            'num_res' => '25',
            'total' => '99',
        ], TagTopTagsPaginationDto::class);

        $this->assertSame(100, $dto->offset);
        $this->assertSame(25, $dto->numRes);
        $this->assertSame(99, $dto->total);
    }
}
