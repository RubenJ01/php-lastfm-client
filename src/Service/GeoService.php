<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Geo\GeoArtistDto;
use Rjds\PhpLastfmClient\Dto\Geo\GeoTrackDto;

final readonly class GeoService extends AbstractService
{
    /**
     * Get the most popular artists by country.
     *
     * @see https://lastfm-docs.github.io/api-docs/geo/getTopArtists/
     *
     * @return PaginatedResponse<GeoArtistDto>
     */
    public function getTopArtists(string $country, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate(
            'geo.gettopartists',
            ['country' => $country, 'limit' => $limit, 'page' => $page],
            'topartists',
            'artist',
            GeoArtistDto::class,
        );
    }

    /**
     * Get the most popular tracks by country.
     *
     * @see https://lastfm-docs.github.io/api-docs/geo/getTopTracks/
     *
     * @return PaginatedResponse<GeoTrackDto>
     */
    public function getTopTracks(string $country, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate(
            'geo.gettoptracks',
            ['country' => $country, 'limit' => $limit, 'page' => $page],
            'tracks',
            'track',
            GeoTrackDto::class,
        );
    }
}
