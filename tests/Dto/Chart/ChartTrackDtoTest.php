<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Chart;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Chart\ChartTrackDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;

final class ChartTrackDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), ChartTrackDto::class);

        $this->assertSame('Stateside', $dto->name);
        $this->assertSame('https://www.last.fm/music/PinkPantheress/_/Stateside', $dto->url);
        $this->assertSame('ffbf7862-2476-4164-ac32-f5904ccefe0f', $dto->mbid);
        $this->assertSame(176, $dto->duration);
        $this->assertSame(13571831, $dto->playcount);
        $this->assertSame(958644, $dto->listeners);
        $this->assertFalse($dto->streamable);
    }

    #[Test]
    public function itMapsArtistData(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), ChartTrackDto::class);

        $this->assertSame('PinkPantheress', $dto->artistName);
        $this->assertSame('https://www.last.fm/music/PinkPantheress', $dto->artistUrl);
        $this->assertSame('7441014f-f8f5-494f-81db-ff166fbc078d', $dto->artistMbid);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), ChartTrackDto::class);

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
    private static function trackApiData(): array
    {
        return [
            'name' => 'Stateside',
            'url' => 'https://www.last.fm/music/PinkPantheress/_/Stateside',
            'mbid' => 'ffbf7862-2476-4164-ac32-f5904ccefe0f',
            'duration' => '176',
            'playcount' => '13571831',
            'listeners' => '958644',
            'streamable' => [
                '#text' => '0',
                'fulltrack' => '0',
            ],
            'artist' => [
                'name' => 'PinkPantheress',
                'mbid' => '7441014f-f8f5-494f-81db-ff166fbc078d',
                'url' => 'https://www.last.fm/music/PinkPantheress',
            ],
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
