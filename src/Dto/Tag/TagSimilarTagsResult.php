<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Tag;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

/**
 * @param list<TagSimilarDto> $tags
 */
final readonly class TagSimilarTagsResult
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<TagSimilarDto> $tags
     */
    public function __construct(
        public ?string $sourceTag,
        public array $tags,
    ) {
    }
}
