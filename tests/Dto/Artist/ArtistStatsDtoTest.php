<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistStatsDto;

final class ArtistStatsDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromStatsArray(): void
    {
        $dto = $this->mapper->map([
            'listeners' => '100',
            'playcount' => '200',
            'userplaycount' => '5',
        ], ArtistStatsDto::class);

        $this->assertSame(100, $dto->listeners);
        $this->assertSame(200, $dto->playcount);
        $this->assertSame(5, $dto->userPlaycount);
    }

    #[Test]
    public function itAllowsMissingUserPlaycount(): void
    {
        $dto = $this->mapper->map([
            'listeners' => '1',
            'playcount' => '2',
        ], ArtistStatsDto::class);

        $this->assertNull($dto->userPlaycount);
    }
}
