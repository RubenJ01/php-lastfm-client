<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Tag;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TagWikiDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $summary = '',
        public string $content = '',
    ) {
    }
}
