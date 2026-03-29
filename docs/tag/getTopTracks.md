# tag.getTopTracks

Get top tracks for a tag, ordered by tag count.

**Last.fm API Reference:** [tag.getTopTracks](https://lastfm-docs.github.io/api-docs/tag/getTopTracks/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->tag()->getTopTracks('metal');
```

## Parameters

| Parameter | Type     | Required | Default | Description |
|-----------|----------|----------|---------|-------------|
| `$tag`    | `string` | Yes      | —       | The tag name. |
| `$limit`  | `int`    | No       | `50`    | Results per page. |
| `$page`   | `int`    | No       | `1`     | Page number. |

## Return Type

Returns a `PaginatedResponse<TagTopTrackDto>` object.

### TagTopTrackDto Properties

| Property     | Type             | Description |
|--------------|------------------|-------------|
| `name`       | `string`         | Track title. |
| `duration`   | `int`            | Duration in seconds. |
| `mbid`       | `string`         | Track MusicBrainz ID. |
| `url`        | `string`         | Last.fm track URL. |
| `artistName` | `string`         | Artist name. |
| `artistMbid` | `?string`        | Artist MBID. |
| `artistUrl`  | `?string`        | Artist URL. |
| `streamable` | `bool`           | From `streamable.#text`. |
| `playcount`  | `int`            | Often `0` when not returned. |
| `listeners`  | `int`            | Often `0` when not returned. |
| `rank`       | `int`            | Position in the chart. |
| `images`     | `list<ImageDto>` | Track images. |

## Examples

```php
$result = $client->tag()->getTopTracks('metal', limit: 20);

foreach ($result->items as $track) {
    echo "{$track->artistName} — {$track->name}\n";
}
```
