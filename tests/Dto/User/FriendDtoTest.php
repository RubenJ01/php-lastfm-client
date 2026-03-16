<?php

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\User\FriendDto;

class FriendDtoTest extends TestCase
{
    private DtoMapper $mapper;

    public function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), FriendDto::class);

        $this->assertSame(0, $dto->playlists);
        $this->assertSame(4, $dto->playcount);
        $this->assertSame(false, $dto->subscriber);
        $this->assertSame('oldmaneatintwix', $dto->name);
        $this->assertSame('United Kingdom', $dto->country);
        $this->assertSame('https://www.last.fm/user/oldmaneatintwix', $dto->url);
        $this->assertSame('Charlie Brown', $dto->realname);
        $this->assertSame(false, $dto->bootstrap);
        $this->assertSame('user', $dto->type);
    }

    #[Test]
    public function itParsesImages(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), FriendDto::class);

        $this->assertCount(4, $dto->images);
        $this->assertInstanceOf(ImageDto::class, $dto->images[0]);
        $this->assertSame('small', $dto->images[0]->size);
        $this->assertSame(
            'https://lastfm.freetls.fastly.net/i/u/34s/1c3fd3c2548906f6ea0f2863718c2668.png',
            $dto->images[0]->url,
        );
    }

    #[Test]
    public function itMapsRegisteredDate(): void
    {
        $dto = $this->mapper->map(self::trackApiData(), FriendDto::class);

        $this->assertSame(1603189685, $dto->registered->getTimestamp());
    }

    /**
     * @return array<string, mixed>
     */
    private static function trackApiData(): array
    {
        return [
            "playlists" => "0",
            "playcount" => "4",
            "subscriber" => "0",
            "name" => "oldmaneatintwix",
            "country" => "United Kingdom",
            "image" => [
                [
                    "size" => "small",
                    "#text" => "https://lastfm.freetls.fastly.net/i/u/34s/1c3fd3c2548906f6ea0f2863718c2668.png"
                ],
                [
                    "size" => "medium",
                    "#text" => "https://lastfm.freetls.fastly.net/i/u/64s/1c3fd3c2548906f6ea0f2863718c2668.png"
                ],
                [
                    "size" => "large",
                    "#text" => "https://lastfm.freetls.fastly.net/i/u/174s/1c3fd3c2548906f6ea0f2863718c2668.png"
                ],
                [
                    "size" => "extralarge",
                    "#text" => "https://lastfm.freetls.fastly.net/i/u/300x300/1c3fd3c2548906f6ea0f2863718c2668.png"
                ]
            ],
            "registered" => [
                "unixtime" => "1603189685",
                "#text" => "2020-10-20 10:28"
            ],
            "url" => "https://www.last.fm/user/oldmaneatintwix",
            "realname" => "Charlie Brown",
            "bootstrap" => "0",
            "type" => "user"
        ];
    }
}
