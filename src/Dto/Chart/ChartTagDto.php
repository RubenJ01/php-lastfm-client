<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Chart;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ChartTagDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $name,
        public string $url,
        #[CastTo('int')]
        public int $reach,
        #[CastTo('int')]
        public int $taggings,
        #[CastTo('bool')]
        public bool $streamable,
    ) {
    }
}
