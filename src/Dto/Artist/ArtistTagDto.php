<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Artist;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ArtistTagDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $name,
        public string $url,
    ) {
    }
}
