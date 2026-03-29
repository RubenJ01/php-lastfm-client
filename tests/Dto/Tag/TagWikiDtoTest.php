<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Tag\TagWikiDto;

final class TagWikiDtoTest extends TestCase
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
            'summary' => 'Short summary.',
            'content' => 'Full wiki body.',
        ], TagWikiDto::class);

        $this->assertSame('Short summary.', $dto->summary);
        $this->assertSame('Full wiki body.', $dto->content);
    }

    #[Test]
    public function itUsesDefaultEmptyStringsWhenKeysMissing(): void
    {
        $dto = $this->mapper->map([], TagWikiDto::class);

        $this->assertSame('', $dto->summary);
        $this->assertSame('', $dto->content);
    }
}
