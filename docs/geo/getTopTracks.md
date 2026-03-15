# geo.getTopTracks

Get the most popular tracks by country.

**Last.fm API Reference:** [geo.getTopTracks](https://lastfm-docs.github.io/api-docs/geo/getTopTracks/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->geo()->getTopTracks('germany');
```

## Parameters

| Parameter  | Type     | Required | Default | Description                    |
|------------|----------|----------|---------|--------------------------------|
| `$country` | `string` | Yes      | —       | The country name (ISO 3166-1). |
| `$limit`   | `int`    | No       | `50`    | Number of results per page.    |
| `$page`    | `int`    | No       | `1`     | The page number to fetch.      |

## Return Type

Returns a `PaginatedResponse<GeoTrackDto>` object.

### GeoTrackDto Properties

| Property     | Type             | Description                              |
|--------------|------------------|------------------------------------------|
| `name`       | `string`         | The track name.                          |
| `url`        | `string`         | URL to the track's Last.fm page.         |
| `mbid`       | `string`         | MusicBrainz ID.                          |
| `duration`   | `int`            | Track duration in seconds.               |
| `listeners`  | `int`            | Number of listeners in this country.     |
| `artistName` | `string`         | The artist's name.                       |
| `artistUrl`  | `string`         | URL to the artist's Last.fm page.        |
| `artistMbid` | `string`         | Artist's MusicBrainz ID.                 |
| `streamable` | `bool`           | Whether the track is streamable.         |
| `images`     | `list<ImageDto>` | Track images in various sizes.           |
| `rank`       | `int`            | Position in the chart.                   |

## Examples

### Basic Usage

```php
$result = $client->geo()->getTopTracks('germany');

foreach ($result->items as $track) {
    echo "#{$track->rank} {$track->artistName} - {$track->name}: {$track->listeners} listeners\n";
}
```

### Pagination

```php
$result = $client->geo()->getTopTracks('france', limit: 10, page: 2);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
```
