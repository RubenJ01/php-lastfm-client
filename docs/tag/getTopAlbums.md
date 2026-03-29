# tag.getTopAlbums

Get top albums for a tag, ordered by tag count.

**Last.fm API Reference:** [tag.getTopAlbums](https://lastfm-docs.github.io/api-docs/tag/getTopAlbums/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->tag()->getTopAlbums('metal');
```

## Parameters

| Parameter | Type     | Required | Default | Description |
|-----------|----------|----------|---------|-------------|
| `$tag`    | `string` | Yes      | —       | The tag name. |
| `$limit`  | `int`    | No       | `50`    | Results per page. |
| `$page`   | `int`    | No       | `1`     | Page number. |

## Return Type

Returns a `PaginatedResponse<TagTopAlbumDto>` object.

### PaginatedResponse Properties

| Property     | Type              | Description |
|--------------|-------------------|-------------|
| `items`      | `list<TagTopAlbumDto>` | Albums on this page. |
| `pagination` | `PaginationDto`   | Page-based metadata (`page`, `perPage`, `total`, `totalPages`). |

### TagTopAlbumDto Properties

| Property     | Type             | Description |
|--------------|------------------|-------------|
| `name`       | `string`         | Album title. |
| `url`        | `string`         | Last.fm album URL. |
| `mbid`       | `string`         | MusicBrainz ID. |
| `artistName` | `string`         | Artist name (from nested `artist`). |
| `artistMbid` | `?string`        | Artist MBID. |
| `artistUrl`  | `?string`        | Artist Last.fm URL. |
| `playcount`  | `int`            | Play count when present (often `0` for this endpoint). |
| `rank`       | `int`            | Position in the chart. |
| `images`     | `list<ImageDto>` | Cover art. |

## Examples

```php
$result = $client->tag()->getTopAlbums('metal', limit: 10);

foreach ($result->items as $album) {
    echo "{$album->rank}. {$album->name} — {$album->artistName}\n";
}
```
