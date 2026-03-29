<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Tag\TagGlobalTopTagDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTagsPaginationDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTagsResult;

final class TagTopTagsResultTest extends TestCase
{
    #[Test]
    public function itHoldsTagsAndPagination(): void
    {
        $tags = [new TagGlobalTopTagDto('rock', 1, 2)];
        $pagination = new TagTopTagsPaginationDto(0, 50, 1000);
        $result = new TagTopTagsResult($tags, $pagination);

        $this->assertSame($tags, $result->tags);
        $this->assertSame($pagination, $result->pagination);
        $this->assertSame(50, $result->pagination->numRes);
    }
}
