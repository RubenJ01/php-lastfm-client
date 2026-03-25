<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class TrackWikiDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $published,
        public string $summary,
        public string $content,
    ) {
    }
}
