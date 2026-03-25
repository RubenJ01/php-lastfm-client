<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Track\TrackWikiDto;

final class TrackWikiDtoTest extends TestCase
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
            'published' => '14 Apr 2009, 16:25',
            'summary' => 'summary',
            'content' => 'content',
        ], TrackWikiDto::class);

        $this->assertSame('14 Apr 2009, 16:25', $dto->published);
        $this->assertSame('summary', $dto->summary);
        $this->assertSame('content', $dto->content);
    }
}
