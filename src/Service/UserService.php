<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\UserDto;
use Rjds\PhpLastfmClient\LastfmClient;

final readonly class UserService
{
    public function __construct(
        private LastfmClient $client,
    ) {
    }

    /**
     * Get information about a user profile.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getInfo/
     */
    public function getInfo(string $user): UserDto
    {
        $response = $this->client->call('user.getinfo', ['user' => $user]);

        /** @var array{name: string, realname: string, url: string, country: string, age: string, subscriber: string, playcount: string, artist_count: string, track_count: string, album_count: string, playlists: string, image: list<array{size: string, '#text': string}>, registered: array{unixtime: string, '#text': int}, type: string} $userData */
        $userData = $response['user'];

        return UserDto::fromArray($userData);
    }
}
