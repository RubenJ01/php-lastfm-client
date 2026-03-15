<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\ImageDto;
use Rjds\PhpLastfmClient\Dto\UserDto;

final class UserDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::userApiResponse(), UserDto::class);

        $this->assertSame('RJ', $dto->name);
        $this->assertSame('Richard Jones', $dto->realname);
        $this->assertSame('https://www.last.fm/user/RJ', $dto->url);
        $this->assertSame('United Kingdom', $dto->country);
        $this->assertSame(0, $dto->age);
        $this->assertTrue($dto->subscriber);
        $this->assertSame(150316, $dto->playcount);
        $this->assertSame(12749, $dto->artistCount);
        $this->assertSame(57066, $dto->trackCount);
        $this->assertSame(26658, $dto->albumCount);
        $this->assertSame(0, $dto->playlists);
        $this->assertSame('alum', $dto->type);
        $this->assertSame(1037793040, $dto->registered->getTimestamp());
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::userApiResponse(), UserDto::class);

        $this->assertCount(2, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
        $this->assertSame('https://lastfm.freetls.fastly.net/i/u/34s/image.png', $dto->images[0]->url);
        $this->assertSame('large', $dto->images[1]->size);
        $this->assertSame('https://lastfm.freetls.fastly.net/i/u/174s/image.png', $dto->images[1]->url);
    }

    #[Test]
    public function itHandlesNonSubscriber(): void
    {
        $data = self::userApiResponse();
        $data['subscriber'] = '0';

        $dto = $this->mapper->map($data, UserDto::class);

        $this->assertFalse($dto->subscriber);
    }

    /**
     * @return array<string, mixed>
     */
    private static function userApiResponse(): array
    {
        return [
            'name' => 'RJ',
            'realname' => 'Richard Jones',
            'url' => 'https://www.last.fm/user/RJ',
            'country' => 'United Kingdom',
            'age' => '0',
            'subscriber' => '1',
            'playcount' => '150316',
            'artist_count' => '12749',
            'track_count' => '57066',
            'album_count' => '26658',
            'playlists' => '0',
            'image' => [
                ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/image.png'],
                ['size' => 'large', '#text' => 'https://lastfm.freetls.fastly.net/i/u/174s/image.png'],
            ],
            'registered' => ['unixtime' => '1037793040', '#text' => 1037793040],
            'type' => 'alum',
        ];
    }
}
