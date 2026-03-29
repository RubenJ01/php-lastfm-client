<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistSummaryDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final class ArtistSummaryDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Drake',
            'url' => 'https://www.last.fm/music/Drake',
            'image' => [
                ['size' => 'small', '#text' => 'https://example.com/s.png'],
            ],
        ], ArtistSummaryDto::class);

        $this->assertSame('Drake', $dto->name);
        $this->assertSame('https://www.last.fm/music/Drake', $dto->url);
        $this->assertCount(1, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
    }
}
