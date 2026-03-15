<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

final readonly class ScrobbleResponseDto
{
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
