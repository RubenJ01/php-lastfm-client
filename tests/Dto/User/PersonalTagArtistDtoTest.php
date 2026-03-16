<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagArtistDto;

final class PersonalTagArtistDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::artistData(), PersonalTagArtistDto::class);

        $this->assertSame('Jack Bruce', $dto->name);
        $this->assertSame('https://www.last.fm/music/Jack+Bruce', $dto->url);
        $this->assertSame('artist-mbid-123', $dto->mbid);
    }

    #[Test]
    public function itMapsStreamableFalse(): void
    {
        $dto = $this->mapper->map(self::artistData(), PersonalTagArtistDto::class);

        $this->assertFalse($dto->streamable);
    }

    #[Test]
    public function itMapsStreamableTrue(): void
    {
        $data = self::artistData();
        $data['streamable'] = '1';

        $dto = $this->mapper->map($data, PersonalTagArtistDto::class);

        $this->assertTrue($dto->streamable);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::artistData(), PersonalTagArtistDto::class);

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
    private static function artistData(): array
    {
        return [
            'name' => 'Jack Bruce',
            'mbid' => 'artist-mbid-123',
            'url' => 'https://www.last.fm/music/Jack+Bruce',
            'streamable' => '0',
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
