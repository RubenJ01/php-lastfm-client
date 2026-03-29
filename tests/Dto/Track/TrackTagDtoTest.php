<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Track\TrackTagDto;

final class TrackTagDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponseWithCount(): void
    {
        $dto = $this->mapper->map([
            'name' => 'hard rock',
            'url' => 'https://www.last.fm/tag/hard+rock',
            'count' => 100,
        ], TrackTagDto::class);

        $this->assertSame('hard rock', $dto->name);
        $this->assertSame('https://www.last.fm/tag/hard+rock', $dto->url);
        $this->assertSame(100, $dto->count);
    }

    #[Test]
    public function itMapsFromApiResponseWithoutCount(): void
    {
        $dto = $this->mapper->map([
            'name' => 'rock',
            'url' => 'https://www.last.fm/tag/rock',
        ], TrackTagDto::class);

        $this->assertSame('rock', $dto->name);
        $this->assertNull($dto->count);
    }
}
