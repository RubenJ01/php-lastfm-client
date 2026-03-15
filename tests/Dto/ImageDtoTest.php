<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\ImageDto;

final class ImageDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiData(): void
    {
        $dto = $this->mapper->map([
            'size' => 'large',
            '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/image.png',
        ], ImageDto::class);

        $this->assertSame('large', $dto->size);
        $this->assertSame('https://lastfm.freetls.fastly.net/i/u/174s/image.png', $dto->url);
    }
}
