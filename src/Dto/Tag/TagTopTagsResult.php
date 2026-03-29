<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Tag;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

/**
 * Result of tag.getTopTags (global top tags, offset-based metadata).
 *
 * @param list<TagGlobalTopTagDto> $tags
 */
final readonly class TagTopTagsResult
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<TagGlobalTopTagDto> $tags
     */
    public function __construct(
        public array $tags,
        public TagTopTagsPaginationDto $pagination,
    ) {
    }
}
