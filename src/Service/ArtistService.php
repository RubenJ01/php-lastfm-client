<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Artist\ArtistBioDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistCorrectionArtistDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistCorrectionDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistSearchResultDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistStatsDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistSummaryDto;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistTagDto;
use Rjds\PhpLastfmClient\Dto\Artist\SimilarArtistDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackTagDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopAlbumDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTagDto;
use Rjds\PhpLastfmClient\Dto\User\UserTopTrackDto;

final readonly class ArtistService extends AbstractService
{
    /**
     * Check whether the supplied artist name has a correction to a canonical artist.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/getCorrection/
     */
    public function getCorrection(string $artist): ArtistCorrectionDto
    {
        $response = $this->client->call('artist.getcorrection', ['artist' => $artist]);

        $corrections = $response['corrections'] ?? null;
        if ($corrections === null || is_string($corrections) || (is_array($corrections) && $corrections === [])) {
            throw new \RuntimeException('No artist correction available for this query.');
        }

        if (!is_array($corrections)) {
            throw new \RuntimeException('Unexpected artist.getCorrection response.');
        }

        /** @var array<string, mixed>|list<array<string, mixed>> $correctionRaw */
        $correctionRaw = $corrections['correction'] ?? [];
        if ($correctionRaw === []) {
            throw new \RuntimeException('No artist correction available for this query.');
        }

        /** @var array<string, mixed> $correction */
        $correction = $this->normalizeToList($correctionRaw)[0];

        /** @var array<string, mixed> $artistData */
        $artistData = $correction['artist'];

        $dto = $this->mapper->map($artistData, ArtistCorrectionArtistDto::class);

        /** @var array<string, mixed> $attr */
        $attr = $correction['@attr'] ?? [];
        $index = isset($attr['index']) && is_numeric($attr['index']) ? (int)$attr['index'] : 0;

        return new ArtistCorrectionDto($dto, $index);
    }

    /**
     * Get artist metadata (bio, similar artists, tags, stats).
     *
     * Provide either `$artist` or `$mbid`.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/getInfo/
     */
    public function getInfo(
        ?string $artist = null,
        ?string $mbid = null,
        bool $autocorrect = false,
        ?string $username = null,
        ?string $lang = null,
    ): ArtistDto {
        $params = $this->artistParams($artist, $mbid, $autocorrect);

        if ($username !== null) {
            $params['username'] = $username;
        }

        if ($lang !== null) {
            $params['lang'] = $lang;
        }

        $response = $this->client->call('artist.getinfo', $params);

        /** @var array<string, mixed> $artistData */
        $artistData = $response['artist'];

        return $this->mapArtistInfo($artistData);
    }

    /**
     * Get artists similar to this artist.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/getSimilar/
     *
     * @return list<SimilarArtistDto>
     */
    public function getSimilar(
        ?string $artist = null,
        ?string $mbid = null,
        bool $autocorrect = false,
        int $limit = 30,
    ): array {
        $params = array_merge($this->artistParams($artist, $mbid, $autocorrect), ['limit' => $limit]);

        $response = $this->client->call('artist.getsimilar', $params);

        /** @var array<string, mixed> $data */
        $data = $response['similarartists'];

        /** @var array<string, mixed>|list<array<string, mixed>> $artistPayload */
        $artistPayload = $data['artist'] ?? [];
        $list = $this->normalizeToList($artistPayload);

        /** @var list<SimilarArtistDto> $items */
        $items = [];
        foreach ($list as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, SimilarArtistDto::class);
        }

        return $items;
    }

    /**
     * Get tags a user has applied to an artist.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/getTags/
     *
     * @return list<TrackTagDto>
     */
    public function getTags(
        string $user,
        ?string $artist = null,
        ?string $mbid = null,
        bool $autocorrect = false,
    ): array {
        $params = array_merge(
            $this->artistParams($artist, $mbid, $autocorrect),
            ['user' => $user],
        );

        $response = $this->client->call('artist.gettags', $params);

        /** @var array<string, mixed> $data */
        $data = $response['tags'];

        /** @var array<string, mixed>|list<array<string, mixed>> $tagPayload */
        $tagPayload = $data['tag'] ?? [];
        $list = $this->normalizeToList($tagPayload);

        /** @var list<TrackTagDto> $items */
        $items = [];
        foreach ($list as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, TrackTagDto::class);
        }

        return $items;
    }

    /**
     * Get top albums for an artist.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/getTopAlbums/
     *
     * @return PaginatedResponse<UserTopAlbumDto>
     */
    public function getTopAlbums(
        ?string $artist = null,
        ?string $mbid = null,
        bool $autocorrect = false,
        int $limit = 50,
        int $page = 1,
    ): PaginatedResponse {
        $params = array_merge(
            $this->artistParams($artist, $mbid, $autocorrect),
            ['limit' => $limit, 'page' => $page],
        );

        return $this->paginateArtist(
            'artist.gettopalbums',
            $params,
            'topalbums',
            'album',
            UserTopAlbumDto::class,
        );
    }

    /**
     * Get top tags for an artist.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/getTopTags/
     *
     * @return list<UserTopTagDto>
     */
    public function getTopTags(
        ?string $artist = null,
        ?string $mbid = null,
        bool $autocorrect = false,
    ): array {
        $params = $this->artistParams($artist, $mbid, $autocorrect);

        $response = $this->client->call('artist.gettoptags', $params);

        /** @var array<string, mixed> $data */
        $data = $response['toptags'];

        /** @var array<string, mixed>|list<array<string, mixed>> $tagPayload */
        $tagPayload = $data['tag'] ?? [];
        $list = $this->normalizeToList($tagPayload);

        /** @var list<UserTopTagDto> $items */
        $items = [];
        foreach ($list as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, UserTopTagDto::class);
        }

        return $items;
    }

    /**
     * Get top tracks for an artist.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/getTopTracks/
     *
     * @return PaginatedResponse<UserTopTrackDto>
     */
    public function getTopTracks(
        ?string $artist = null,
        ?string $mbid = null,
        bool $autocorrect = false,
        int $limit = 50,
        int $page = 1,
    ): PaginatedResponse {
        $params = array_merge(
            $this->artistParams($artist, $mbid, $autocorrect),
            ['limit' => $limit, 'page' => $page],
        );

        return $this->paginateArtist(
            'artist.gettoptracks',
            $params,
            'toptracks',
            'track',
            UserTopTrackDto::class,
        );
    }

    /**
     * Search for artists by name.
     *
     * @see https://lastfm-docs.github.io/api-docs/artist/search/
     *
     * @return PaginatedResponse<ArtistSearchResultDto>
     */
    public function search(string $artist, int $limit = 30, int $page = 1): PaginatedResponse
    {
        $response = $this->client->call('artist.search', [
            'artist' => $artist,
            'limit' => $limit,
            'page' => $page,
        ]);

        /** @var array<string, mixed> $results */
        $results = $response['results'];

        /** @var array<string, mixed> $matches */
        $matches = $results['artistmatches'];

        /** @var array<string, mixed>|list<array<string, mixed>> $artistPayload */
        $artistPayload = $matches['artist'] ?? [];
        $artistList = $this->normalizeToList($artistPayload);

        /** @var list<ArtistSearchResultDto> $items */
        $items = [];
        foreach ($artistList as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, ArtistSearchResultDto::class);
        }

        $total = self::toInt($results['opensearch:totalResults'] ?? 0);
        $perPage = self::toInt($results['opensearch:itemsPerPage'] ?? $limit);

        $query = isset($results['opensearch:Query']) && is_array($results['opensearch:Query'])
            ? $results['opensearch:Query']
            : [];
        $currentPage = isset($query['startPage']) ? self::toInt($query['startPage']) : $page;
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 0;

        $pagination = new PaginationDto(
            page: $currentPage,
            perPage: $perPage,
            total: $total,
            totalPages: $totalPages,
        );

        return new PaginatedResponse($items, $pagination);
    }

    /**
     * @param array<string, mixed> $artistData
     */
    private function mapArtistInfo(array $artistData): ArtistDto
    {
        $images = [];
        if (isset($artistData['image']) && is_array($artistData['image']) && array_is_list($artistData['image'])) {
            foreach ($artistData['image'] as $img) {
                if (is_array($img)) {
                    /** @var array<string, mixed> $img */
                    $images[] = $this->mapper->map($img, ImageDto::class);
                }
            }
        }

        $statsData = isset($artistData['stats']) && is_array($artistData['stats']) ? $artistData['stats'] : [];
        $stats = $statsData === []
            ? new ArtistStatsDto(0, 0, null)
            : $this->mapper->map($statsData, ArtistStatsDto::class);

        $bio = null;
        if (isset($artistData['bio']) && is_array($artistData['bio'])) {
            /** @var array<string, mixed> $bioData */
            $bioData = $artistData['bio'];
            $bio = new ArtistBioDto(
                published: isset($bioData['published']) && is_string($bioData['published']) ?
                    $bioData['published'] : '',
                summary: isset($bioData['summary']) && is_string($bioData['summary']) ?
                    $bioData['summary'] : '',
                content: isset($bioData['content']) && is_string($bioData['content']) ?
                    $bioData['content'] : '',
            );
        }

        $similarArtists = [];
        if (isset($artistData['similar']) && is_array($artistData['similar'])) {
            /** @var array<string, mixed> $similar */
            $similar = $artistData['similar'];
            /** @var array<string, mixed>|list<array<string, mixed>> $simPayload */
            $simPayload = $similar['artist'] ?? [];
            foreach ($this->normalizeToList($simPayload) as $sim) {
                /** @var array<string, mixed> $sim */
                $similarArtists[] = $this->mapper->map($sim, ArtistSummaryDto::class);
            }
        }

        $tags = [];
        if (isset($artistData['tags']) && is_array($artistData['tags'])) {
            /** @var array<string, mixed> $tagsWrap */
            $tagsWrap = $artistData['tags'];
            /** @var array<string, mixed>|list<array<string, mixed>> $tagPayload */
            $tagPayload = $tagsWrap['tag'] ?? [];
            foreach ($this->normalizeToList($tagPayload) as $tag) {
                /** @var array<string, mixed> $tag */
                $tags[] = $this->mapper->map($tag, ArtistTagDto::class);
            }
        }

        return new ArtistDto(
            name: self::toString($artistData['name'] ?? ''),
            mbid: self::toString($artistData['mbid'] ?? ''),
            url: self::toString($artistData['url'] ?? ''),
            streamable: self::toBool($artistData['streamable'] ?? false),
            onTour: self::toBool($artistData['ontour'] ?? false),
            stats: $stats,
            bio: $bio,
            images: $images,
            similarArtists: $similarArtists,
            tags: $tags,
        );
    }

    /**
     * @return array<string, string|int>
     */
    private function artistParams(?string $artist, ?string $mbid, bool $autocorrect): array
    {
        if ($mbid === null && $artist === null) {
            throw new \InvalidArgumentException('Provide either an mbid or an artist name.');
        }

        $params = [];
        if ($mbid !== null) {
            $params['mbid'] = $mbid;
        } else {
            $params['artist'] = $artist;
        }

        if ($autocorrect) {
            $params['autocorrect'] = 1;
        }

        return $params;
    }

    /**
     * @template T of object
     *
     * @param array<string, string|int> $params
     * @param class-string<T> $dtoClass
     * @return PaginatedResponse<T>
     */
    private function paginateArtist(
        string $method,
        array $params,
        string $wrapperKey,
        string $itemsKey,
        string $dtoClass,
    ): PaginatedResponse {
        $response = $this->client->call($method, $params);

        /** @var array<string, mixed> $data */
        $data = $response[$wrapperKey];

        /** @var array<string, mixed>|list<array<string, mixed>> $itemPayload */
        $itemPayload = $data[$itemsKey] ?? [];
        $itemList = $this->normalizeToList($itemPayload);

        /** @var list<T> $items */
        $items = [];
        foreach ($itemList as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, $dtoClass);
        }

        /** @var array<string, mixed> $attrData */
        $attrData = $data['@attr'];
        $pagination = $this->mapper->map($attrData, PaginationDto::class);

        return new PaginatedResponse($items, $pagination);
    }

    /**
     * @param array<string, mixed>|list<array<string, mixed>> $payload
     * @return list<array<string, mixed>>
     */
    private function normalizeToList(array $payload): array
    {
        if ($payload === []) {
            return [];
        }

        if (array_is_list($payload)) {
            /** @var list<array<string, mixed>> $payload */
            return $payload;
        }

        /** @var array<string, mixed> $payload */
        return [$payload];
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            return $normalized === '1' || $normalized === 'true' || $normalized === 'yes';
        }

        return (bool)$value;
    }

    private static function toInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int)$value;
        }

        if (is_float($value)) {
            return (int)$value;
        }

        return 0;
    }

    private static function toString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string)$value;
        }

        return '';
    }
}
