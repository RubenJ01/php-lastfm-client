<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Tag\TagSimilarDto;

final class TagSimilarDtoTest extends TestCase
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
            'name' => 'heavy metal',
            'url' => 'https://www.last.fm/tag/heavy+metal',
            'streamable' => '1',
            'match' => '0.75',
        ], TagSimilarDto::class);

        $this->assertSame('heavy metal', $dto->name);
        $this->assertSame('https://www.last.fm/tag/heavy+metal', $dto->url);
        $this->assertTrue($dto->streamable);
        $this->assertSame('0.75', $dto->match);
    }

    #[Test]
    public function itUsesDefaultsWhenOptionalFieldsMissing(): void
    {
        $dto = $this->mapper->map(['name' => 'rock'], TagSimilarDto::class);

        $this->assertSame('rock', $dto->name);
        $this->assertSame('', $dto->url);
        $this->assertFalse($dto->streamable);
        $this->assertNull($dto->match);
    }
}
