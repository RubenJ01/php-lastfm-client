<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

final readonly class ImageDto
{
    public function __construct(
        public string $size,
        public string $url,
    ) {
    }

    /**
     * @param array{size: string, '#text': string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            size: $data['size'],
            url: $data['#text'],
        );
    }
}
