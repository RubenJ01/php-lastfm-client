<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Library\LibraryArtistDto;

final readonly class LibraryService extends AbstractService
{
    /**
     * Get a paginated list of all the artists in a user's library.
     *
     * @see https://lastfm-docs.github.io/api-docs/library/getArtists/
     *
     * @return PaginatedResponse<LibraryArtistDto>
     */
    public function getArtists(string $user, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate('library.getartists', [
            'user' => $user,
            'limit' => $limit,
            'page' => $page,
        ], 'artists', 'artist', LibraryArtistDto::class);
    }
}
