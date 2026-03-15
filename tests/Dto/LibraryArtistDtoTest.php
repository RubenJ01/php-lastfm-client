<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\ImageDto;
use Rjds\PhpLastfmClient\Dto\LibraryArtistDto;

final class LibraryArtistDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), LibraryArtistDto::class);

        $this->assertSame('Queen', $dto->name);
        $this->assertSame('https://www.last.fm/music/Queen', $dto->url);
        $this->assertSame('5eecaf18-02ec-47af-a4f2-7831db373419', $dto->mbid);
        $this->assertSame(0, $dto->tagcount);
        $this->assertSame(1511, $dto->playcount);
        $this->assertFalse($dto->streamable);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::artistApiData(), LibraryArtistDto::class);

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
            'name' => 'Queen',
            'url' => 'https://www.last.fm/music/Queen',
            'mbid' => '5eecaf18-02ec-47af-a4f2-7831db373419',
            'tagcount' => '0',
            'playcount' => '1511',
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
