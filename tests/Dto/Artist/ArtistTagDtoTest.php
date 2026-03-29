<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistTagDto;

final class ArtistTagDtoTest extends TestCase
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
            'name' => 'rnb',
            'url' => 'https://www.last.fm/tag/rnb',
        ], ArtistTagDto::class);

        $this->assertSame('rnb', $dto->name);
        $this->assertSame('https://www.last.fm/tag/rnb', $dto->url);
    }
}
