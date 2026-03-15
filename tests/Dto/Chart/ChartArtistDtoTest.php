<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Chart;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Chart\ChartArtistDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final class ChartArtistDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), ChartArtistDto::class);

        $this->assertSame('PinkPantheress', $dto->name);
        $this->assertSame('https://www.last.fm/music/PinkPantheress', $dto->url);
        $this->assertSame('7441014f-f8f5-494f-81db-ff166fbc078d', $dto->mbid);
        $this->assertSame(308772499, $dto->playcount);
        $this->assertSame(2964326, $dto->listeners);
        $this->assertFalse($dto->streamable);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), ChartArtistDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
        $this->assertSame(
            'https://lastfm.freetls.fastly.net/i/u/34s/2a96cbd8b46e442fc41c2b86b821562f.png',
            $dto->images[0]->url,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function artistApiData(): array
    {
        return [
            'name' => 'PinkPantheress',
            'url' => 'https://www.last.fm/music/PinkPantheress',
            'mbid' => '7441014f-f8f5-494f-81db-ff166fbc078d',
            'playcount' => '308772499',
            'listeners' => '2964326',
            'streamable' => '0',
            'image' => [
                [
                    'size' => 'small',
                    '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/2a96cbd8b46e442fc41c2b86b821562f.png',
                ],
                [
                    'size' => 'large',
                    '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/2a96cbd8b46e442fc41c2b86b821562f.png',
                ],
            ],
        ];
    }
}
