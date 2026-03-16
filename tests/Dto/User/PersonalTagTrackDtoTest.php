<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagTrackDto;

final class PersonalTagTrackDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::trackData(), PersonalTagTrackDto::class);

        $this->assertSame('Arc Of A Diver', $dto->name);
        $this->assertSame(
            'https://www.last.fm/music/Steve+Winwood/_/Arc+Of+A+Diver',
            $dto->url,
        );
        $this->assertSame('track-mbid-123', $dto->mbid);
        $this->assertSame('FIXME', $dto->duration);
    }

    #[Test]
    public function itMapsArtistFields(): void
    {
        $dto = $this->mapper->map(self::trackData(), PersonalTagTrackDto::class);

        $this->assertSame('Steve Winwood', $dto->artistName);
        $this->assertSame(
            'https://www.last.fm/music/Steve+Winwood',
            $dto->artistUrl,
        );
        $this->assertSame('artist-mbid-456', $dto->artistMbid);
    }

    #[Test]
    public function itMapsStreamableFalse(): void
    {
        $dto = $this->mapper->map(self::trackData(), PersonalTagTrackDto::class);

        $this->assertFalse($dto->streamable);
    }

    #[Test]
    public function itMapsStreamableTrue(): void
    {
        $data = self::trackData();
        $data['streamable'] = ['#text' => '1', 'fulltrack' => '1'];

        $dto = $this->mapper->map($data, PersonalTagTrackDto::class);

        $this->assertTrue($dto->streamable);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::trackData(), PersonalTagTrackDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
        $this->assertSame(
            'https://lastfm.freetls.fastly.net/i/u/34s/img.png',
            $dto->images[0]->url,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function trackData(): array
    {
        return [
            'name' => 'Arc Of A Diver',
            'duration' => 'FIXME',
            'mbid' => 'track-mbid-123',
            'url' => 'https://www.last.fm/music/Steve+Winwood/_/Arc+Of+A+Diver',
            'streamable' => [
                '#text' => '0',
                'fulltrack' => '0',
            ],
            'artist' => [
                'name' => 'Steve Winwood',
                'mbid' => 'artist-mbid-456',
                'url' => 'https://www.last.fm/music/Steve+Winwood',
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
        ];
    }
}
