<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\LibraryArtistDto;
use Rjds\PhpLastfmClient\Dto\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\PaginationDto;
use Rjds\PhpLastfmClient\LastfmClient;

final readonly class LibraryService
{
    public function __construct(
        private LastfmClient $client,
        private DtoMapper $mapper = new DtoMapper(),
    ) {
    }

    /**
     * Get a paginated list of all the artists in a user's library.
     *
     * @see https://lastfm-docs.github.io/api-docs/library/getArtists/
     *
     * @return PaginatedResponse<LibraryArtistDto>
     */
    public function getArtists(string $user, int $limit = 50, int $page = 1): PaginatedResponse
    {
        $response = $this->client->call('library.getartists', [
            'user' => $user,
            'limit' => (string) $limit,
            'page' => (string) $page,
        ]);

        /** @var array<string, mixed> $data */
        $data = $response['artists'];

        /** @var list<array<string, mixed>> $artistList */
        $artistList = $data['artist'];

        /** @var list<LibraryArtistDto> $artists */
        $artists = [];
        foreach ($artistList as $item) {
            $artists[] = $this->mapper->map($item, LibraryArtistDto::class);
        }

        /** @var array<string, mixed> $attrData */
        $attrData = $data['@attr'];
        $pagination = $this->mapper->map($attrData, PaginationDto::class);

        return new PaginatedResponse($artists, $pagination);
    }
}
