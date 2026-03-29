<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Tag\TagGlobalTopTagDto;

final class TagGlobalTopTagDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map([
            'name' => 'rock',
            'count' => '4024829',
            'reach' => '399440',
        ], TagGlobalTopTagDto::class);

        $this->assertSame('rock', $dto->name);
        $this->assertSame(4024829, $dto->count);
        $this->assertSame(399440, $dto->reach);
    }

    #[Test]
    public function itMapsNumericJsonValues(): void
    {
        $dto = $this->mapper->map([
            'name' => 'electronic',
            'count' => 100,
            'reach' => 50,
        ], TagGlobalTopTagDto::class);

        $this->assertSame(100, $dto->count);
        $this->assertSame(50, $dto->reach);
    }
}
