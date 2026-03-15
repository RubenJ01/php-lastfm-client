<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

use Rjds\PhpDto\Attribute\MapFrom;

final readonly class ImageDto
{
    public function __construct(
        public string $size,
        #[MapFrom('#text')]
        public string $url,
    ) {
    }
}
