# chart.getTopTracks

Get the top tracks chart.

**Last.fm API Reference:** [chart.getTopTracks](https://lastfm-docs.github.io/api-docs/chart/getTopTracks/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->chart()->getTopTracks();
```

## Parameters

| Parameter | Type  | Required | Default | Description                    |
|-----------|-------|----------|---------|--------------------------------|
| `$limit`  | `int` | No       | `50`    | Number of results per page.    |
| `$page`   | `int` | No       | `1`     | The page number to fetch.      |

## Return Type

Returns a `PaginatedResponse<ChartTrackDto>` object.

### PaginatedResponse Properties

| Property     | Type                    | Description                    |
|--------------|-------------------------|--------------------------------|
| `items`      | `list<ChartTrackDto>`   | The tracks on this page.       |
| `pagination` | `PaginationDto`         | Pagination metadata.           |

### ChartTrackDto Properties

| Property     | Type             | Description                              |
|--------------|------------------|------------------------------------------|
| `name`       | `string`         | The track name.                          |
| `url`        | `string`         | URL to the track's Last.fm page.         |
| `mbid`       | `string`         | MusicBrainz ID.                          |
| `duration`   | `int`            | Track duration in seconds.               |
| `playcount`  | `int`            | Total play count.                        |
| `listeners`  | `int`            | Total number of listeners.               |
| `artistName` | `string`         | The artist's name.                       |
| `artistUrl`  | `string`         | URL to the artist's Last.fm page.        |
| `artistMbid` | `string`         | Artist's MusicBrainz ID.                 |
| `streamable` | `bool`           | Whether the track is streamable.         |
| `images`     | `list<ImageDto>` | Track images in various sizes.           |

## Examples

### Basic Usage

```php
$result = $client->chart()->getTopTracks();

foreach ($result->items as $track) {
    echo "{$track->artistName} - {$track->name}: {$track->playcount} plays\n";
}
```

### Pagination

```php
$result = $client->chart()->getTopTracks(limit: 10, page: 2);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
```
