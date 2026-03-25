<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\User\WeeklyChartRangeDto;

final class WeeklyChartRangeDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(['from' => '100', 'to' => '200'], WeeklyChartRangeDto::class);

        $this->assertSame(100, $dto->from);
        $this->assertSame(200, $dto->to);
    }
}
