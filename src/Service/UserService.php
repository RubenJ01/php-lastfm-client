<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\Dto\User\LovedTrackDto;
use Rjds\PhpLastfmClient\Dto\User\UserDto;
use Rjds\PhpLastfmClient\LastfmClient;

final readonly class UserService
{
    public function __construct(
        private LastfmClient $client,
        private DtoMapper $mapper = new DtoMapper(),
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
        $response = $this->client->call('user.getlovedtracks', [
            'user' => $user,
            'limit' => $limit,
            'page' => $page,
        ]);

        /** @var array<string, mixed> $data */
        $data = $response['lovedtracks'];

        /** @var list<array<string, mixed>> $trackList */
        $trackList = $data['track'];

        /** @var list<LovedTrackDto> $tracks */
        $tracks = [];
        foreach ($trackList as $item) {
            $tracks[] = $this->mapper->map($item, LovedTrackDto::class);
        }

        /** @var array<string, mixed> $attrData */
        $attrData = $data['@attr'];
        $pagination = $this->mapper->map($attrData, PaginationDto::class);

        return new PaginatedResponse($tracks, $pagination);
    }
}
