<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Chart;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Chart\ChartTagDto;

final class ChartTagDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::tagApiData(), ChartTagDto::class);

        $this->assertSame('rock', $dto->name);
        $this->assertSame('https://www.last.fm/tag/rock', $dto->url);
        $this->assertSame(402881, $dto->reach);
        $this->assertSame(4069101, $dto->taggings);
        $this->assertTrue($dto->streamable);
    }

    /**
     * @return array<string, mixed>
     */
    private static function tagApiData(): array
    {
        return [
            'name' => 'rock',
            'url' => 'https://www.last.fm/tag/rock',
            'reach' => '402881',
            'taggings' => '4069101',
            'streamable' => '1',
            'wiki' => [],
        ];
    }
}
