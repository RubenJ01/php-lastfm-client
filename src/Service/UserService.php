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
use Rjds\PhpLastfmClient\Dto\User\RecentTrackDto;
use Rjds\PhpLastfmClient\Dto\User\UserDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopAlbumDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopArtistDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTagDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTrackDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyAlbumChartItemDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyArtistChartItemDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyChartRangeDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyTrackChartItemDto;

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

    /**
     * Get a paginated list of a user's recent tracks (scrobbles).
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getRecentTracks/
     *
     * @return PaginatedResponse<RecentTrackDto>
     */
    public function getRecentTracks(
        string $user,
        int $limit = 50,
        int $page = 1,
        ?int $from = null,
        ?int $to = null,
        bool $extended = false,
    ): PaginatedResponse {
        $params = [
            'user' => $user,
            'limit' => $limit,
            'page' => $page,
        ];

        if ($from !== null) {
            $params['from'] = $from;
        }

        if ($to !== null) {
            $params['to'] = $to;
        }

        if ($extended) {
            $params['extended'] = 1;
        }

        $response = $this->client->call('user.getrecenttracks', $params);

        /** @var array<string, mixed> $data */
        $data = $response['recenttracks'];

        /** @var array<string, mixed>|list<array<string, mixed>> $trackData */
        $trackData = $data['track'];

        // Some responses return a single object instead of a list
        if (!array_is_list($trackData)) {
            $trackData = [$trackData];
        }

        /** @var list<RecentTrackDto> $items */
        $items = [];
        foreach ($trackData as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, RecentTrackDto::class);
        }

        /** @var array<string, mixed> $attrData */
        $attrData = $data['@attr'];
        $pagination = $this->mapper->map($attrData, PaginationDto::class);

        return new PaginatedResponse($items, $pagination);
    }

    /**
     * Get a paginated list of a user's top artists.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getTopArtists/
     *
     * @return PaginatedResponse<UserTopArtistDto>
     */
    public function getTopArtists(
        string $user,
        string $period = 'overall',
        int $limit = 50,
        int $page = 1,
    ): PaginatedResponse {
        return $this->paginate(
            'user.gettopartists',
            ['user' => $user, 'period' => $period, 'limit' => $limit, 'page' => $page],
            'topartists',
            'artist',
            UserTopArtistDto::class,
        );
    }

    /**
     * Get a paginated list of a user's top albums.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getTopAlbums/
     *
     * @return PaginatedResponse<UserTopAlbumDto>
     */
    public function getTopAlbums(
        string $user,
        string $period = 'overall',
        int $limit = 50,
        int $page = 1,
    ): PaginatedResponse {
        return $this->paginate(
            'user.gettopalbums',
            ['user' => $user, 'period' => $period, 'limit' => $limit, 'page' => $page],
            'topalbums',
            'album',
            UserTopAlbumDto::class,
        );
    }

    /**
     * Get a paginated list of a user's top tracks.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getTopTracks/
     *
     * @return PaginatedResponse<UserTopTrackDto>
     */
    public function getTopTracks(
        string $user,
        string $period = 'overall',
        int $limit = 50,
        int $page = 1,
    ): PaginatedResponse {
        return $this->paginate(
            'user.gettoptracks',
            ['user' => $user, 'period' => $period, 'limit' => $limit, 'page' => $page],
            'toptracks',
            'track',
            UserTopTrackDto::class,
        );
    }

    /**
     * Get a paginated list of a user's top tags.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getTopTags/
     *
     * @return PaginatedResponse<UserTopTagDto>
     */
    public function getTopTags(string $user, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate(
            'user.gettoptags',
            ['user' => $user, 'limit' => $limit, 'page' => $page],
            'toptags',
            'tag',
            UserTopTagDto::class,
        );
    }

    /**
     * Get the list of available weekly chart date ranges for a user.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getWeeklyChartList/
     *
     * @return list<WeeklyChartRangeDto>
     */
    public function getWeeklyChartList(string $user): array
    {
        $response = $this->client->call('user.getweeklychartlist', ['user' => $user]);

        /** @var array<string, mixed> $data */
        $data = $response['weeklychartlist'];

        /** @var array<string, mixed>|list<array<string, mixed>> $chartData */
        $chartData = $data['chart'];

        // Sometimes a single range may be returned as an object
        if (!array_is_list($chartData)) {
            $chartData = [$chartData];
        }

        /** @var list<WeeklyChartRangeDto> $items */
        $items = [];
        foreach ($chartData as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, WeeklyChartRangeDto::class);
        }

        return $items;
    }

    /**
     * Get a user's weekly artist chart for a given date range.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getWeeklyArtistChart/
     *
     * @return list<WeeklyArtistChartItemDto>
     */
    public function getWeeklyArtistChart(
        string $user,
        ?int $from = null,
        ?int $to = null,
    ): array {
        $params = ['user' => $user];

        if ($from !== null) {
            $params['from'] = $from;
        }

        if ($to !== null) {
            $params['to'] = $to;
        }

        $response = $this->client->call('user.getweeklyartistchart', $params);

        /** @var array<string, mixed> $data */
        $data = $response['weeklyartistchart'];

        /** @var array<string, mixed>|list<array<string, mixed>> $artistData */
        $artistData = $data['artist'];

        if (!array_is_list($artistData)) {
            $artistData = [$artistData];
        }

        /** @var list<WeeklyArtistChartItemDto> $items */
        $items = [];
        foreach ($artistData as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, WeeklyArtistChartItemDto::class);
        }

        return $items;
    }

    /**
     * Get a user's weekly album chart for a given date range.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getWeeklyAlbumChart/
     *
     * @return list<WeeklyAlbumChartItemDto>
     */
    public function getWeeklyAlbumChart(
        string $user,
        ?int $from = null,
        ?int $to = null,
    ): array {
        $params = ['user' => $user];

        if ($from !== null) {
            $params['from'] = $from;
        }

        if ($to !== null) {
            $params['to'] = $to;
        }

        $response = $this->client->call('user.getweeklyalbumchart', $params);

        /** @var array<string, mixed> $data */
        $data = $response['weeklyalbumchart'];

        /** @var array<string, mixed>|list<array<string, mixed>> $albumData */
        $albumData = $data['album'];

        if (!array_is_list($albumData)) {
            $albumData = [$albumData];
        }

        /** @var list<WeeklyAlbumChartItemDto> $items */
        $items = [];
        foreach ($albumData as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, WeeklyAlbumChartItemDto::class);
        }

        return $items;
    }

    /**
     * Get a user's weekly track chart for a given date range.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getWeeklyTrackChart/
     *
     * @return list<WeeklyTrackChartItemDto>
     */
    public function getWeeklyTrackChart(
        string $user,
        ?int $from = null,
        ?int $to = null,
    ): array {
        $params = ['user' => $user];

        if ($from !== null) {
            $params['from'] = $from;
        }

        if ($to !== null) {
            $params['to'] = $to;
        }

        $response = $this->client->call('user.getweeklytrackchart', $params);

        /** @var array<string, mixed> $data */
        $data = $response['weeklytrackchart'];

        /** @var array<string, mixed>|list<array<string, mixed>> $trackData */
        $trackData = $data['track'];

        if (!array_is_list($trackData)) {
            $trackData = [$trackData];
        }

        /** @var list<WeeklyTrackChartItemDto> $items */
        $items = [];
        foreach ($trackData as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, WeeklyTrackChartItemDto::class);
        }

        return $items;
    }
}
