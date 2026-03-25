<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackSearchResultDto;

final class TrackSearchResultDtoTest extends TestCase
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
            'name' => 'Hells Bells',
            'artist' => 'AC/DC',
            'url' => 'https://www.last.fm/music/AC%2FDC/_/Hells+Bells',
            'listeners' => '796758',
            'image' => [
                ['#text' => 'https://lastfm/1.png', 'size' => 'small'],
                ['#text' => 'https://lastfm/2.png', 'size' => 'large'],
            ],
            'mbid' => 'b6411d6b-2dca-4004-8919-e8c27ff6b286',
        ], TrackSearchResultDto::class);

        $this->assertSame('Hells Bells', $dto->name);
        $this->assertSame('AC/DC', $dto->artistName);
        $this->assertSame(796758, $dto->listeners);
        $this->assertSame('b6411d6b-2dca-4004-8919-e8c27ff6b286', $dto->mbid);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
    }
}
