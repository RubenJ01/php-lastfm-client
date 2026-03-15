# chart.getTopArtists

Get the top artists chart.

**Last.fm API Reference:** [chart.getTopArtists](https://lastfm-docs.github.io/api-docs/chart/getTopArtists/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->chart()->getTopArtists();
```

## Parameters

| Parameter | Type  | Required | Default | Description                    |
|-----------|-------|----------|---------|--------------------------------|
| `$limit`  | `int` | No       | `50`    | Number of results per page.    |
| `$page`   | `int` | No       | `1`     | The page number to fetch.      |

## Return Type

Returns a `PaginatedResponse<ChartArtistDto>` object.

### PaginatedResponse Properties

| Property     | Type                     | Description                    |
|--------------|--------------------------|--------------------------------|
| `items`      | `list<ChartArtistDto>`   | The artists on this page.      |
| `pagination` | `PaginationDto`          | Pagination metadata.           |

### ChartArtistDto Properties

| Property     | Type             | Description                              |
|--------------|------------------|------------------------------------------|
| `name`       | `string`         | The artist's name.                       |
| `url`        | `string`         | URL to the artist's Last.fm page.        |
| `mbid`       | `string`         | MusicBrainz ID.                          |
| `playcount`  | `int`            | Total play count.                        |
| `listeners`  | `int`            | Total number of listeners.               |
| `streamable` | `bool`           | Whether the artist is streamable.        |
| `images`     | `list<ImageDto>` | Artist images in various sizes.          |

## Examples

### Basic Usage

```php
$result = $client->chart()->getTopArtists();

foreach ($result->items as $artist) {
    echo "{$artist->name}: {$artist->playcount} plays, {$artist->listeners} listeners\n";
}
```

### Pagination

```php
$result = $client->chart()->getTopArtists(limit: 10, page: 2);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
```
