<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\RecentTrackDto;

final class RecentTrackDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::recentTrackApiData(), RecentTrackDto::class);

        $this->assertSame('Karma Police', $dto->name);
        $this->assertSame('https://www.last.fm/music/Radiohead/_/Karma+Police', $dto->url);
        $this->assertSame('track-mbid-1', $dto->mbid);
        $this->assertSame('Radiohead', $dto->artistName);
        $this->assertSame('artist-mbid-1', $dto->artistMbid);
        $this->assertSame('OK Computer', $dto->albumName);
        $this->assertFalse($dto->nowPlaying);
        $this->assertNotNull($dto->scrobbledAt);
        $this->assertSame(1603112664, $dto->scrobbledAt->getTimestamp());
    }

    #[Test]
    public function itMapsNowPlayingTrack(): void
    {
        $data = self::recentTrackApiData();
        unset($data['date']);
        $data['@attr'] = ['nowplaying' => 'true'];

        $dto = $this->mapper->map($data, RecentTrackDto::class);

        $this->assertTrue($dto->nowPlaying);
        $this->assertNull($dto->scrobbledAt);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::recentTrackApiData(), RecentTrackDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
    }

    /**
     * @return array<string, mixed>
     */
    private static function recentTrackApiData(): array
    {
        return [
            'name' => 'Karma Police',
            'mbid' => 'track-mbid-1',
            'url' => 'https://www.last.fm/music/Radiohead/_/Karma+Police',
            'artist' => [
                '#text' => 'Radiohead',
                'mbid' => 'artist-mbid-1',
            ],
            'album' => [
                '#text' => 'OK Computer',
            ],
            'image' => [
                ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png'],
                ['size' => 'large', '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png'],
            ],
            'date' => [
                'uts' => '1603112664',
                '#text' => '19 Oct 2020, 13:04',
            ],
        ];
    }
}
