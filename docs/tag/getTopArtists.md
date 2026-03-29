# tag.getTopArtists

Get top artists for a tag, ordered by tag count.

**Last.fm API Reference:** [tag.getTopArtists](https://lastfm-docs.github.io/api-docs/tag/getTopArtists/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->tag()->getTopArtists('metal');
```

## Parameters

| Parameter | Type     | Required | Default | Description |
|-----------|----------|----------|---------|-------------|
| `$tag`    | `string` | Yes      | —       | The tag name. |
| `$limit`  | `int`    | No       | `50`    | Results per page. |
| `$page`   | `int`    | No       | `1`     | Page number. |

## Return Type

Returns a `PaginatedResponse<TagTopArtistDto>` object.

### TagTopArtistDto Properties

| Property     | Type             | Description |
|--------------|------------------|-------------|
| `name`       | `string`         | Artist name. |
| `url`        | `string`         | Last.fm artist URL. |
| `mbid`       | `string`         | MusicBrainz ID. |
| `streamable` | `bool`           | Streamable flag. |
| `playcount`  | `int`            | Often `0` when not returned by the API. |
| `rank`       | `int`            | Position in the chart. |
| `images`     | `list<ImageDto>` | Artist images. |

## Examples

```php
$result = $client->tag()->getTopArtists('metal');

foreach ($result->items as $artist) {
    echo "{$artist->rank}. {$artist->name}\n";
}
```
