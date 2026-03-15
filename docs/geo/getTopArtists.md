# geo.getTopArtists

Get the most popular artists by country.

**Last.fm API Reference:** [geo.getTopArtists](https://lastfm-docs.github.io/api-docs/geo/getTopArtists/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->geo()->getTopArtists('germany');
```

## Parameters

| Parameter  | Type     | Required | Default | Description                    |
|------------|----------|----------|---------|--------------------------------|
| `$country` | `string` | Yes      | —       | The country name (ISO 3166-1). |
| `$limit`   | `int`    | No       | `50`    | Number of results per page.    |
| `$page`    | `int`    | No       | `1`     | The page number to fetch.      |

## Return Type

Returns a `PaginatedResponse<GeoArtistDto>` object.

### GeoArtistDto Properties

| Property     | Type             | Description                              |
|--------------|------------------|------------------------------------------|
| `name`       | `string`         | The artist's name.                       |
| `url`        | `string`         | URL to the artist's Last.fm page.        |
| `mbid`       | `string`         | MusicBrainz ID.                          |
| `listeners`  | `int`            | Number of listeners in this country.     |
| `streamable` | `bool`           | Whether the artist is streamable.        |
| `images`     | `list<ImageDto>` | Artist images in various sizes.          |
| `rank`       | `int`            | Position in the chart.                   |

## Examples

### Basic Usage

```php
$result = $client->geo()->getTopArtists('germany');

foreach ($result->items as $artist) {
    echo "#{$artist->rank} {$artist->name}: {$artist->listeners} listeners\n";
}
```

### Pagination

```php
$result = $client->geo()->getTopArtists('france', limit: 10, page: 2);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
```
