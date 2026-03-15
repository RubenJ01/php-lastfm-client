<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Chart\ChartArtistDto;
use Rjds\PhpLastfmClient\Dto\Chart\ChartTagDto;
use Rjds\PhpLastfmClient\Dto\Chart\ChartTrackDto;
use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;

final readonly class ChartService extends AbstractService
{
    /**
     * Get the top artists chart.
     *
     * @see https://lastfm-docs.github.io/api-docs/chart/getTopArtists/
     *
     * @return PaginatedResponse<ChartArtistDto>
     */
    public function getTopArtists(int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate(
            'chart.gettopartists',
            ['limit' => $limit, 'page' => $page],
            'artists',
            'artist',
            ChartArtistDto::class,
        );
    }

    /**
     * Get the top tags chart.
     *
     * @see https://lastfm-docs.github.io/api-docs/chart/getTopTags/
     *
     * @return PaginatedResponse<ChartTagDto>
     */
    public function getTopTags(int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate(
            'chart.gettoptags',
            ['limit' => $limit, 'page' => $page],
            'tags',
            'tag',
            ChartTagDto::class,
        );
    }

    /**
     * Get the top tracks chart.
     *
     * @see https://lastfm-docs.github.io/api-docs/chart/getTopTracks/
     *
     * @return PaginatedResponse<ChartTrackDto>
     */
    public function getTopTracks(int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginate(
            'chart.gettoptracks',
            ['limit' => $limit, 'page' => $page],
            'tracks',
            'track',
            ChartTrackDto::class,
        );
    }
}
