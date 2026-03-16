<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\User\FriendDto;
use Rjds\PhpLastfmClient\Dto\User\LovedTrackDto;
use Rjds\PhpLastfmClient\Dto\User\UserDto;

final readonly class UserService extends AbstractService
{
    /**
     * Get information about a user profile.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getInfo/
     */
    public function getInfo(string $user): UserDto
    {
        $response = $this->client->call('user.getinfo', ['user' => $user]);

        /** @var array<string, mixed> $userData */
        $userData = $response['user'];

        return $this->mapper->map($userData, UserDto::class);
    }

    /**
     * Get a paginated list of tracks loved by a user.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getLovedTracks/
     *
     * @return PaginatedResponse<LovedTrackDto>
     */
    public function getLovedTracks(string $user, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate('user.getlovedtracks', [
            'user' => $user,
            'limit' => $limit,
            'page' => $page,
        ], 'lovedtracks', 'track', LovedTrackDto::class);
    }

    /**
     * Get a paginated list of a users friends.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getFriends/
     *
     * @return PaginatedResponse<FriendDto>
     */
    public function getFriends(string $user, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate('user.getfriends', [
            'user' => $user,
            'limit' => $limit,
            'page' => $page,
        ], 'friends', 'user', FriendDto::class);
    }
}
