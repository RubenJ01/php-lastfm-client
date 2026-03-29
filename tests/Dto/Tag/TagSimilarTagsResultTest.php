<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Tag;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Tag\TagSimilarDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagSimilarTagsResult;

final class TagSimilarTagsResultTest extends TestCase
{
    #[Test]
    public function itHoldsSourceTagAndSimilarList(): void
    {
        $similar = [new TagSimilarDto('prog', 'https://www.last.fm/tag/prog')];
        $result = new TagSimilarTagsResult('metal', $similar);

        $this->assertSame('metal', $result->sourceTag);
        $this->assertSame($similar, $result->tags);
        $this->assertSame('prog', $result->tags[0]->name);
    }

    #[Test]
    public function itAllowsNullSourceTag(): void
    {
        $result = new TagSimilarTagsResult(null, []);

        $this->assertNull($result->sourceTag);
        $this->assertSame([], $result->tags);
    }
}
