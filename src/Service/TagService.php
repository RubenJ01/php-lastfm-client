<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagGlobalTopTagDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagInfoDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagSimilarDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagSimilarTagsResult;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopAlbumDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopArtistDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTagsPaginationDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTagsResult;
use Rjds\PhpLastfmClient\Dto\Tag\TagTopTrackDto;
use Rjds\PhpLastfmClient\Dto\Tag\TagWikiDto;
use Rjds\PhpLastfmClient\Dto\User\WeeklyChartRangeDto;

final readonly class TagService extends AbstractService
{
    /**
     * Get metadata for a tag.
     *
     * @see https://lastfm-docs.github.io/api-docs/tag/getInfo/
     */
    public function getInfo(string $tag, ?string $lang = null): TagInfoDto
    {
        $params = ['tag' => $tag];
        if ($lang !== null) {
            $params['lang'] = $lang;
        }

        $response = $this->client->call('tag.getinfo', $params);

        /** @var array<string, mixed> $tagData */
        $tagData = $response['tag'];

        /** @var array<string, mixed> $wikiRaw */
        $wikiRaw = $tagData['wiki'] ?? [];
        if ($wikiRaw === []) {
            $wikiRaw = ['summary' => '', 'content' => ''];
        }

        $wiki = $this->mapper->map($wikiRaw, TagWikiDto::class);

        $name = $tagData['name'];
        $total = $tagData['total'];
        $reach = $tagData['reach'];
        if (!is_string($name) || !is_numeric($total) || !is_numeric($reach)) {
            throw new \RuntimeException('Invalid tag.getinfo response: missing name, total, or reach.');
        }

        return new TagInfoDto(
            name: $name,
            total: (int) $total,
            reach: (int) $reach,
            wiki: $wiki,
        );
    }

    /**
     * Get tags similar to the given tag.
     *
     * @see https://lastfm-docs.github.io/api-docs/tag/getSimilar/
     */
    public function getSimilar(string $tag): TagSimilarTagsResult
    {
        $response = $this->client->call('tag.getsimilar', ['tag' => $tag]);

        /** @var array<string, mixed> $data */
        $data = $response['similartags'];

        /** @var array<string, mixed>|list<array<string, mixed>> $tagPayload */
        $tagPayload = $data['tag'] ?? [];
        $itemList = $this->normalizeToList($tagPayload);

        /** @var list<TagSimilarDto> $items */
        $items = [];
        foreach ($itemList as $item) {
            /** @var array<string, mixed> $item */
            $items[] = $this->mapper->map($item, TagSimilarDto::class);
        }

        /** @var array<string, mixed> $attr */
        $attr = $data['@attr'];
        $sourceTag = isset($attr['tag']) && is_string($attr['tag']) && $attr['tag'] !== 'n/a'
            ? $attr['tag']
            : null;

        return new TagSimilarTagsResult($sourceTag, $items);
    }

    /**
     * Get top albums for a tag.
     *
     * @see https://lastfm-docs.github.io/api-docs/tag/getTopAlbums/
     *
     * @return PaginatedResponse<TagTopAlbumDto>
     */
    public function getTopAlbums(string $tag, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginateTag(
            'tag.gettopalbums',
            ['tag' => $tag, 'limit' => $limit, 'page' => $page],
            'albums',
            'album',
            TagTopAlbumDto::class,
        );
    }

    /**
     * Get top artists for a tag.
     *
     * @see https://lastfm-docs.github.io/api-docs/tag/getTopArtists/
     *
     * @return PaginatedResponse<TagTopArtistDto>
     */
    public function getTopArtists(string $tag, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginateTag(
            'tag.gettopartists',
            ['tag' => $tag, 'limit' => $limit, 'page' => $page],
            'topartists',
            'artist',
            TagTopArtistDto::class,
        );
    }

    /**
     * Get global top tags (sorted by popularity).
     *
     * @see https://lastfm-docs.github.io/api-docs/tag/getTopTags/
     */
    public function getTopTags(?int $limit = null, ?int $offset = null): TagTopTagsResult
    {
        $params = [];
        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        if ($offset !== null) {
            $params['offset'] = $offset;
        }

        $response = $this->client->call('tag.gettoptags', $params);

        /** @var array<string, mixed> $data */
        $data = $response['toptags'];

        /** @var array<string, mixed>|list<array<string, mixed>> $tagPayload */
        $tagPayload = $data['tag'] ?? [];
        $itemList = $this->normalizeToList($tagPayload);

        /** @var list<TagGlobalTopTagDto> $tags */
        $tags = [];
        foreach ($itemList as $item) {
            /** @var array<string, mixed> $item */
            $tags[] = $this->mapper->map($item, TagGlobalTopTagDto::class);
        }

        /** @var array<string, mixed> $attrData */
        $attrData = $data['@attr'];
        $pagination = $this->mapper->map($attrData, TagTopTagsPaginationDto::class);

        return new TagTopTagsResult($tags, $pagination);
    }

    /**
     * Get top tracks for a tag.
     *
     * @see https://lastfm-docs.github.io/api-docs/tag/getTopTracks/
     *
     * @return PaginatedResponse<TagTopTrackDto>
     */
    public function getTopTracks(string $tag, int $limit = 50, int $page = 1): PaginatedResponse
    {
        return $this->paginateTag(
            'tag.gettoptracks',
            ['tag' => $tag, 'limit' => $limit, 'page' => $page],
            'tracks',
            'track',
            TagTopTrackDto::class,
        );
    }

    /**
     * Get weekly chart date ranges available for a tag.
     *
     * @see https://lastfm-docs.github.io/api-docs/tag/getWeeklyChartList/
     *
     * @return list<WeeklyChartRangeDto>
     */
    public function getWeeklyChartList(string $tag): array
    {
        $response = $this->client->call('tag.getweeklychartlist', ['tag' => $tag]);

        /** @var array<string, mixed> $data */
        $data = $response['weeklychartlist'];

        /** @var array<string, mixed>|list<array<string, mixed>> $chartData */
        $chartData = $data['chart'];

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

    /**
     * @template T of object
     *
     * @param array<string, string|int> $params
     * @param class-string<T> $dtoClass
     * @return PaginatedResponse<T>
     */
    private function paginateTag(
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
}
