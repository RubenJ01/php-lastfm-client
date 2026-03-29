<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Tag\TagInfoDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagWikiDto;

final class TagInfoDtoTest extends TestCase
{
    #[Test]
    public function itHoldsTagMetadataAndWiki(): void
    {
        $wiki = new TagWikiDto('Summary.', 'Content.');
        $dto = new TagInfoDto('metal', 10, 5, $wiki);

        $this->assertSame('metal', $dto->name);
        $this->assertSame(10, $dto->total);
        $this->assertSame(5, $dto->reach);
        $this->assertSame($wiki, $dto->wiki);
        $this->assertSame('Summary.', $dto->wiki->summary);
    }
}
