<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\LovedTrackDto;

final class LovedTrackDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), LovedTrackDto::class);

        $this->assertSame('Davy Crochet', $dto->name);
        $this->assertSame(
            'https://www.last.fm/music/The+Backseat+Lovers/_/Davy+Crochet',
            $dto->url,
        );
        $this->assertSame('59da79dd-aed6-447c-951c-070f6b8446a1', $dto->mbid);
    }

    #[Test]
    public function itMapsArtistFields(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), LovedTrackDto::class);

        $this->assertSame('The Backseat Lovers', $dto->artistName);
        $this->assertSame(
            'https://www.last.fm/music/The+Backseat+Lovers',
            $dto->artistUrl,
        );
        $this->assertSame('artist-mbid-123', $dto->artistMbid);
    }

    #[Test]
    public function itMapsLovedAtDate(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), LovedTrackDto::class);

        $this->assertSame(1603112664, $dto->lovedAt->getTimestamp());
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), LovedTrackDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
        $this->assertSame(
            'https://lastfm.freetls.fastly.net/i/u/34s/img.png',
            $dto->images[0]->url,
        );
    }

    #[Test]
    public function itMapsStreamableField(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), LovedTrackDto::class);

        $this->assertFalse($dto->streamable);
    }

    #[Test]
    public function itMapsStreamableTrue(): void
    {
        $data = self::trackApiData();
        $data['streamable'] = ['fulltrack' => '1', '#text' => '1'];

        $dto = $this->mapper->map($data, LovedTrackDto::class);

        $this->assertTrue($dto->streamable);
    }

    /**
     * @return array<string, mixed>
     */
    private static function trackApiData(): array
    {
        return [
            'name' => 'Davy Crochet',
            'url' => 'https://www.last.fm/music/The+Backseat+Lovers/_/Davy+Crochet',
            'mbid' => '59da79dd-aed6-447c-951c-070f6b8446a1',
            'artist' => [
                'name' => 'The Backseat Lovers',
                'url' => 'https://www.last.fm/music/The+Backseat+Lovers',
                'mbid' => 'artist-mbid-123',
            ],
            'date' => [
                'uts' => '1603112664',
                '#text' => '19 Oct 2020, 13:04',
            ],
            'image' => [
                [
                    'size' => 'small',
                    '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/img.png',
                ],
                [
                    'size' => 'large',
                    '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/img.png',
                ],
            ],
            'streamable' => [
                'fulltrack' => '0',
                '#text' => '0',
            ],
        ];
    }
}
