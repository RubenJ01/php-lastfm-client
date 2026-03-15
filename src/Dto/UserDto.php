<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

final readonly class UserDto
{
    /**
     * @param list<ImageDto> $images
     */
    public function __construct(
        public string $name,
        public string $realname,
        public string $url,
        public string $country,
        public int $age,
        public bool $subscriber,
        public int $playcount,
        public int $artistCount,
        public int $trackCount,
        public int $albumCount,
        public int $playlists,
        public array $images,
        public \DateTimeImmutable $registered,
        public string $type,
    ) {
    }

    /**
     * @param array{
     *     name: string,
     *     realname: string,
     *     url: string,
     *     country: string,
     *     age: string,
     *     subscriber: string,
     *     playcount: string,
     *     artist_count: string,
     *     track_count: string,
     *     album_count: string,
     *     playlists: string,
     *     image: list<array{size: string, '#text': string}>,
     *     registered: array{unixtime: string, '#text': int},
     *     type: string
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            realname: $data['realname'],
            url: $data['url'],
            country: $data['country'],
            age: (int) $data['age'],
            subscriber: $data['subscriber'] === '1',
            playcount: (int) $data['playcount'],
            artistCount: (int) $data['artist_count'],
            trackCount: (int) $data['track_count'],
            albumCount: (int) $data['album_count'],
            playlists: (int) $data['playlists'],
            images: array_map(ImageDto::fromArray(...), $data['image']),
            registered: (new \DateTimeImmutable())->setTimestamp((int) $data['registered']['unixtime']),
            type: $data['type'],
        );
    }
}
