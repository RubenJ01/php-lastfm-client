<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Track\Scrobble;
use Rjds\PhpLastfmClient\Dto\Track\ScrobbleResponseDto;
use Rjds\PhpLastfmClient\Dto\Track\ScrobbleResultDto;
use Rjds\PhpLastfmClient\Dto\Track\SimilarTrackDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackCorrectionDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackCorrectionTrackDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackAlbumDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackArtistDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackInfoDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackSearchResultDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackTagDto;
use Rjds\PhpLastfmClient\Dto\Track\TrackWikiDto;

final readonly class TrackService extends AbstractService
{
    /**
     * Use the Last.fm corrections data to check whether the supplied track has a correction.
     *
     * @see https://lastfm-docs.github.io/api-docs/track/getCorrection/
     */
    public function getCorrection(string $artist, string $track): TrackCorrectionDto
    {
        $response = $this->client->call('track.getcorrection', [
            'artist' => $artist,
            'track' => $track,
        ]);

        /** @var array<string, mixed> $corrections */
        $corrections = $response['corrections'];

        /** @var array<string, mixed> $correction */
        $correction = $corrections['correction'];

        /** @var array<string, mixed> $trackData */
        $trackData = $correction['track'];

        /** @var array<string, mixed>|null $artistData */
        $artistData = isset($trackData['artist']) && is_array($trackData['artist']) ? $trackData['artist'] : null;
        $artistDto = $artistData !== null
            ? $this->mapper->map($artistData, TrackArtistDto::class)
            : null;

        $trackDto = new TrackCorrectionTrackDto(
            name: isset($trackData['name']) && is_string($trackData['name']) ? $trackData['name'] : null,
            mbid: isset($trackData['mbid']) && is_string($trackData['mbid']) ? $trackData['mbid'] : null,
            url: isset($trackData['url']) && is_string($trackData['url']) ? $trackData['url'] : null,
            artist: $artistDto,
        );

        /** @var array<string, mixed> $attr */
        $attr = $correction['@attr'];

        return new TrackCorrectionDto(
            track: $trackDto,
            artistCorrected: self::toBool($attr['artistcorrected'] ?? false),
            trackCorrected: self::toBool($attr['trackcorrected'] ?? false),
        );
    }

    /**
     * Get the metadata for a track.
     *
     * Provide either (artist + track) or an mbid.
     *
     * @see https://lastfm-docs.github.io/api-docs/track/getInfo/
     */
    public function getInfo(
        ?string $artist = null,
        ?string $track = null,
        ?string $mbid = null,
        bool $autocorrect = false,
        ?string $username = null,
    ): TrackInfoDto {
        if ($mbid === null && ($artist === null || $track === null)) {
            throw new \InvalidArgumentException('Provide either an mbid or both artist and track.');
        }

        $params = [];

        if ($mbid !== null) {
            $params['mbid'] = $mbid;
        } else {
            $params['artist'] = $artist;
            $params['track'] = $track;
        }

        if ($autocorrect) {
            $params['autocorrect'] = 1;
        }

        if ($username !== null) {
            $params['username'] = $username;
        }

        $response = $this->client->call('track.getinfo', $params);

        /** @var array<string, mixed> $trackData */
        $trackData = $response['track'];

        if (!isset($trackData['artist']) || !is_array($trackData['artist'])) {
            throw new \RuntimeException('Unexpected track.getInfo response: missing artist.');
        }

        /** @var array<string, mixed> $artistData */
        $artistData = $trackData['artist'];
        $artistDto = $this->mapper->map($artistData, TrackArtistDto::class);

        $albumDto = null;
        if (isset($trackData['album']) && is_array($trackData['album'])) {
            /** @var array<string, mixed> $albumData */
            $albumData = $trackData['album'];
            $albumDto = $this->mapper->map($albumData, TrackAlbumDto::class);
        }

        $topTags = [];
        if (isset($trackData['toptags']) && is_array($trackData['toptags'])) {
            /** @var array<string, mixed> $toptags */
            $toptags = $trackData['toptags'];
            if (isset($toptags['tag']) && is_array($toptags['tag']) && array_is_list($toptags['tag'])) {
                /** @var list<array<string, mixed>> $tagList */
                $tagList = array_values(array_filter($toptags['tag'], 'is_array'));
                foreach ($tagList as $tag) {
                    $topTags[] = $this->mapper->map($tag, TrackTagDto::class);
                }
            }
        }

        $wikiDto = null;
        if (isset($trackData['wiki']) && is_array($trackData['wiki'])) {
            /** @var array<string, mixed> $wikiData */
            $wikiData = $trackData['wiki'];
            $wikiDto = $this->mapper->map($wikiData, TrackWikiDto::class);
        }

        /** @var array<string, mixed> $streamable */
        $streamable = isset($trackData['streamable']) && is_array($trackData['streamable'])
            ? $trackData['streamable']
            : [];

        return new TrackInfoDto(
            name: self::toString($trackData['name'] ?? ''),
            mbid: isset($trackData['mbid']) && is_string($trackData['mbid']) ? $trackData['mbid'] : null,
            url: self::toString($trackData['url'] ?? ''),
            duration: self::toInt($trackData['duration'] ?? 0),
            streamable: self::toBool($streamable['#text'] ?? false),
            fullTrackStreamable: self::toBool($streamable['fulltrack'] ?? false),
            listeners: self::toInt($trackData['listeners'] ?? 0),
            playcount: self::toInt($trackData['playcount'] ?? 0),
            artist: $artistDto,
            album: $albumDto,
            userPlaycount: isset($trackData['userplaycount']) ? self::toInt($trackData['userplaycount']) : null,
            userLoved: isset($trackData['userloved']) ? self::toBool($trackData['userloved']) : null,
            topTags: $topTags,
            wiki: $wikiDto,
        );
    }

    /**
     * Get the similar tracks for a track.
     *
     * Provide either (artist + track) or an mbid.
     *
     * @see https://lastfm-docs.github.io/api-docs/track/getSimilar/
     *
     * @return list<SimilarTrackDto>
     */
    public function getSimilar(
        ?string $artist = null,
        ?string $track = null,
        ?string $mbid = null,
        bool $autocorrect = false,
        int $limit = 100,
    ): array {
        if ($mbid === null && ($artist === null || $track === null)) {
            throw new \InvalidArgumentException('Provide either an mbid or both artist and track.');
        }

        $params = ['limit' => $limit];

        if ($mbid !== null) {
            $params['mbid'] = $mbid;
        } else {
            $params['artist'] = $artist;
            $params['track'] = $track;
        }

        if ($autocorrect) {
            $params['autocorrect'] = 1;
        }

        $response = $this->client->call('track.getsimilar', $params);

        /** @var array<string, mixed> $data */
        $data = $response['similartracks'];

        /** @var array<string, mixed>|list<array<string, mixed>> $trackData */
        $trackData = $data['track'];

        if (!array_is_list($trackData)) {
            $trackData = [$trackData];
        }

        /** @var list<SimilarTrackDto> $items */
        $items = [];
        foreach ($trackData as $item) {
            /** @var array<string, mixed> $item */
            /** @var array<string, mixed> $artistData */
            if (!isset($item['artist']) || !is_array($item['artist'])) {
                continue;
            }

            /** @var array<mixed, mixed> $artistData */
            $artistData = $item['artist'];
            $artistDto = self::artistFromArray($artistData);

            /** @var array<string, mixed> $streamable */
            $streamable = is_array($item['streamable'] ?? null) ? $item['streamable'] : [];

            $images = [];
            if (isset($item['image']) && is_array($item['image']) && array_is_list($item['image'])) {
                foreach ($item['image'] as $img) {
                    if (is_array($img)) {
                        /** @var array<mixed, mixed> $img */
                        $images[] = self::imageFromArray($img);
                    }
                }
            }

            $items[] = new SimilarTrackDto(
                name: self::toString($item['name'] ?? ''),
                playcount: self::toInt($item['playcount'] ?? 0),
                mbid: self::toString($item['mbid'] ?? ''),
                match: self::toFloat($item['match'] ?? 0.0),
                url: self::toString($item['url'] ?? ''),
                streamable: self::toBool($streamable['#text'] ?? false),
                fullTrackStreamable: self::toBool($streamable['fulltrack'] ?? false),
                duration: self::toInt($item['duration'] ?? 0),
                artist: $artistDto,
                images: $images,
            );
        }

        return $items;
    }

    /**
     * Get the top tags for a track.
     *
     * Provide either (artist + track) or an mbid.
     *
     * @see https://lastfm-docs.github.io/api-docs/track/getTopTags/
     *
     * @return list<TrackTagDto>
     */
    public function getTopTags(
        ?string $artist = null,
        ?string $track = null,
        ?string $mbid = null,
        bool $autocorrect = false,
    ): array {
        if ($mbid === null && ($artist === null || $track === null)) {
            throw new \InvalidArgumentException('Provide either an mbid or both artist and track.');
        }

        $params = [];

        if ($mbid !== null) {
            $params['mbid'] = $mbid;
        } else {
            $params['artist'] = $artist;
            $params['track'] = $track;
        }

        if ($autocorrect) {
            $params['autocorrect'] = 1;
        }

        $response = $this->client->call('track.gettoptags', $params);

        /** @var array<string, mixed> $data */
        $data = $response['toptags'];

        /** @var list<array<string, mixed>> $tagData */
        $tagData = $data['tag'];

        /** @var list<TrackTagDto> $items */
        $items = [];
        foreach ($tagData as $item) {
            $items[] = $this->mapper->map($item, TrackTagDto::class);
        }

        return $items;
    }

    /**
     * Get the tags applied by an individual user to a track.
     *
     * Provide either (artist + track) or an mbid.
     *
     * @see https://lastfm-docs.github.io/api-docs/track/getTags/
     *
     * @return list<TrackTagDto>
     */
    public function getTags(
        string $user,
        ?string $artist = null,
        ?string $track = null,
        ?string $mbid = null,
        bool $autocorrect = false,
    ): array {
        if ($mbid === null && ($artist === null || $track === null)) {
            throw new \InvalidArgumentException('Provide either an mbid or both artist and track.');
        }

        $params = ['user' => $user];

        if ($mbid !== null) {
            $params['mbid'] = $mbid;
        } else {
            $params['artist'] = $artist;
            $params['track'] = $track;
        }

        if ($autocorrect) {
            $params['autocorrect'] = 1;
        }

        $response = $this->client->call('track.gettags', $params);

        /** @var array<string, mixed> $data */
        $data = $response['tags'];

        /** @var list<array<string, mixed>> $tagData */
        $tagData = $data['tag'];

        /** @var list<TrackTagDto> $items */
        $items = [];
        foreach ($tagData as $item) {
            $items[] = $this->mapper->map($item, TrackTagDto::class);
        }

        return $items;
    }

    /**
     * Search for tracks by name (optionally narrowed by artist).
     *
     * @see https://lastfm-docs.github.io/api-docs/track/search/
     *
     * @return PaginatedResponse<TrackSearchResultDto>
     */
    public function search(
        string $track,
        ?string $artist = null,
        int $limit = 30,
        int $page = 1,
    ): PaginatedResponse {
        $params = [
            'track' => $track,
            'limit' => $limit,
            'page' => $page,
        ];

        if ($artist !== null) {
            $params['artist'] = $artist;
        }

        $response = $this->client->call('track.search', $params);

        /** @var array<string, mixed> $results */
        $results = $response['results'];

        /** @var array<string, mixed> $matches */
        $matches = $results['trackmatches'];

        /** @var list<array<string, mixed>> $trackList */
        $trackList = $matches['track'];

        /** @var list<TrackSearchResultDto> $items */
        $items = [];
        foreach ($trackList as $item) {
            $items[] = $this->mapper->map($item, TrackSearchResultDto::class);
        }

        $total = self::toInt($results['opensearch:totalResults'] ?? 0);
        $perPage = self::toInt($results['opensearch:itemsPerPage'] ?? $limit);

        $query = isset($results['opensearch:Query']) && is_array($results['opensearch:Query'])
            ? $results['opensearch:Query']
            : [];
        $currentPage = isset($query['startPage']) ? self::toInt($query['startPage']) : $page;
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;

        $pagination = new PaginationDto(
            page: $currentPage,
            perPage: $perPage,
            total: $total,
            totalPages: $totalPages,
        );

        return new PaginatedResponse($items, $pagination);
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

        return (bool) $value;
    }

    /**
     * @param array<mixed, mixed> $value
     */
    private static function artistFromArray(array $value): TrackArtistDto
    {
        return new TrackArtistDto(
            name: self::toString($value['name'] ?? ''),
            mbid: isset($value['mbid']) ? self::toString($value['mbid']) : null,
            url: isset($value['url']) ? self::toString($value['url']) : null,
        );
    }

    /**
     * @param array<mixed, mixed> $value
     */
    private static function imageFromArray(array $value): ImageDto
    {
        return new ImageDto(
            size: self::toString($value['size'] ?? ''),
            url: self::toString($value['#text'] ?? ''),
        );
    }

    private static function toString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        return '';
    }

    private static function toInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        return 0;
    }

    private static function toFloat(mixed $value): float
    {
        if (is_float($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (float) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        return 0.0;
    }

    /**
     * Scrobble a single track.
     *
     * @see https://www.last.fm/api/show/track.scrobble
     */
    public function scrobble(Scrobble $scrobble): ScrobbleResponseDto
    {
        return $this->scrobbleBatch([$scrobble]);
    }

    /**
     * Scrobble a batch of tracks (up to 50).
     *
     * @param list<Scrobble> $scrobbles The scrobbles to submit
     *
     * @throws \InvalidArgumentException when the batch is empty or exceeds 50
     *
     * @see https://www.last.fm/api/show/track.scrobble
     */
    public function scrobbleBatch(array $scrobbles): ScrobbleResponseDto
    {
        if (count($scrobbles) === 0) {
            throw new \InvalidArgumentException('At least one scrobble is required.');
        }

        if (count($scrobbles) > 50) {
            throw new \InvalidArgumentException(
                'A maximum of 50 scrobbles can be sent per batch.'
            );
        }

        $params = [];
        foreach ($scrobbles as $index => $scrobble) {
            $params = array_merge($params, $scrobble->toParams($index));
        }

        $response = $this->client->callAuthenticated('track.scrobble', $params);

        /** @var array<string, mixed> $scrobblesData */
        $scrobblesData = $response['scrobbles'];

        /** @var array{accepted: int, ignored: int} $attr */
        $attr = $scrobblesData['@attr'];

        $accepted = $attr['accepted'];
        $ignored = $attr['ignored'];

        /** @var array<string, mixed>|list<array<string, mixed>> $scrobbleData */
        $scrobbleData = $scrobblesData['scrobble'];

        // Single scrobble returns an object, batch returns an array
        if (!array_is_list($scrobbleData)) {
            $scrobbleData = [$scrobbleData];
        }

        /** @var list<ScrobbleResultDto> $results */
        $results = [];
        foreach ($scrobbleData as $item) {
            /** @var array<string, mixed> $item */
            $results[] = $this->mapper->map($item, ScrobbleResultDto::class);
        }

        return new ScrobbleResponseDto($accepted, $ignored, $results);
    }
}
