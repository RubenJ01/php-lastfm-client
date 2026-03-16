<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\Dto\User\FriendDto;
use Rjds\PhpLastfmClient\Dto\User\LovedTrackDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagAlbumDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagArtistDto;
use Rjds\PhpLastfmClient\Dto\User\PersonalTagTrackDto;
use Rjds\PhpLastfmClient\Dto\User\UserDto;

final readonly class UserService extends AbstractService
{
    /** @var array<string, array{plural: string, singular: string, dto: class-string}> */
    private const array TAGGING_TYPE_MAP = [
        'artist' => [
            'plural' => 'artists',
            'singular' => 'artist',
            'dto' => PersonalTagArtistDto::class,
        ],
        'album' => [
            'plural' => 'albums',
            'singular' => 'album',
            'dto' => PersonalTagAlbumDto::class,
        ],
        'track' => [
            'plural' => 'tracks',
            'singular' => 'track',
            'dto' => PersonalTagTrackDto::class,
        ],
    ];
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

    /**
     * Get the items a user has tagged with a personal tag.
     *
     * The tagging type determines the response DTO:
     * - 'artist' → PersonalTagArtistDto
     * - 'album'  → PersonalTagAlbumDto
     * - 'track'  → PersonalTagTrackDto
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getPersonalTags/
     *
     * @param string $taggingType One of 'artist', 'album', or 'track'
     *
     * @return PaginatedResponse<PersonalTagArtistDto|PersonalTagAlbumDto|PersonalTagTrackDto>
     */
    public function getPersonalTags(
        string $user,
        string $tag,
        string $taggingType,
        int $limit = 50,
        int $page = 1,
    ): PaginatedResponse {
        if (!isset(self::TAGGING_TYPE_MAP[$taggingType])) {
            throw new \InvalidArgumentException(
                "Invalid tagging type '{$taggingType}'. Must be one of: artist, album, track."
            );
        }

        $config = self::TAGGING_TYPE_MAP[$taggingType];

        $response = $this->client->call('user.getpersonaltags', [
            'user' => $user,
            'tag' => $tag,
            'taggingtype' => $taggingType,
            'limit' => $limit,
            'page' => $page,
        ]);

        /** @var array<string, mixed> $taggings */
        $taggings = $response['taggings'];

        /** @var array<string, mixed> $wrapper */
        $wrapper = $taggings[$config['plural']];

        /** @var list<array<string, mixed>> $itemList */
        $itemList = $wrapper[$config['singular']];

        $items = [];
        foreach ($itemList as $item) {
            $items[] = $this->mapper->map($item, $config['dto']);
        }

        /** @var array<string, mixed> $attrData */
        $attrData = $taggings['@attr'];
        $pagination = $this->mapper->map($attrData, PaginationDto::class);

        return new PaginatedResponse($items, $pagination);
    }
}
