<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ScrobbleResponseDto
{
    use HasToArray;
    use HasToString;

    /**
     * @param list<ScrobbleResultDto> $scrobbles
     */
    public function __construct(
        public int $accepted,
        public int $ignored,
        public array $scrobbles,
    ) {
    }
}
