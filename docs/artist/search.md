# artist.search

Search for artists by name, ordered by relevance.

**Last.fm API Reference:** [artist.search](https://lastfm-docs.github.io/api-docs/artist/search/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->artist()->search('Rammstein');
```

## Parameters

| Parameter | Type     | Required | Default | Description |
|-----------|----------|----------|---------|-------------|
| `$artist` | `string` | Yes      | —       | Search query (artist name). |
| `$limit`  | `int`    | No       | `30`    | Results per page. |
| `$page`   | `int`    | No       | `1`     | Page number. |

## Return Type

Returns `PaginatedResponse<ArtistSearchResultDto>`.

### ArtistSearchResultDto Properties

| Property     | Type             | Description |
|--------------|------------------|-------------|
| `name`       | `string`         | Artist name. |
| `listeners`  | `int`            | Listener count. |
| `mbid`       | `string`         | MusicBrainz ID. |
| `url`        | `string`         | Last.fm URL. |
| `streamable` | `bool`           | Streamable flag. |
| `images`     | `list<ImageDto>` | Images. |

## Examples

```php
$result = $client->artist()->search('Rammstein', limit: 10);

foreach ($result->items as $a) {
    echo "{$a->name} ({$a->listeners} listeners)\n";
}
```
