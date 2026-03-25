<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\User;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class WeeklyChartRangeDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        #[CastTo('int')]
        public int $from,
        #[CastTo('int')]
        public int $to,
    ) {
    }
}
