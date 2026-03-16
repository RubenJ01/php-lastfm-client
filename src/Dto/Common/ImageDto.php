<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Common;

use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ImageDto
{
    use HasToArray;
    use HasToString;

    public function __construct(
        public string $size,
        #[MapFrom('#text')]
        public string $url,
    ) {
    }
}
