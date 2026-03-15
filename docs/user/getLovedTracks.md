# user.getLovedTracks

Get a paginated list of tracks loved by a user.

**Last.fm API Reference:** [user.getLovedTracks](https://lastfm-docs.github.io/api-docs/user/getLovedTracks/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->user()->getLovedTracks('rj');
```

## Parameters

| Parameter | Type     | Required | Default | Description                              |
|-----------|----------|----------|---------|------------------------------------------|
| `$user`   | `string` | Yes      | —       | The username to fetch loved tracks for.  |
| `$limit`  | `int`    | No       | `50`    | Number of results per page (max 1000).   |
| `$page`   | `int`    | No       | `1`     | The page number to fetch.                |

## Return Type

Returns a `PaginatedResponse<LovedTrackDto>` object.

### PaginatedResponse Properties

| Property     | Type                    | Description                   |
|--------------|-------------------------|-------------------------------|
| `items`      | `list<LovedTrackDto>`   | The loved tracks on this page.|
| `pagination` | `PaginationDto`         | Pagination metadata.          |

### PaginationDto Properties

| Property     | Type  | Description                      |
|--------------|-------|----------------------------------|
| `page`       | `int` | The current page number.         |
| `perPage`    | `int` | Number of results per page.      |
| `total`      | `int` | Total number of results.         |
| `totalPages` | `int` | Total number of pages.           |

### LovedTrackDto Properties

| Property     | Type                | Description                                  |
|--------------|---------------------|----------------------------------------------|
| `name`       | `string`            | The track name.                              |
| `url`        | `string`            | URL to the track's Last.fm page.             |
| `mbid`       | `string`            | MusicBrainz Track ID.                        |
| `artistName` | `string`            | The artist's name.                           |
| `artistUrl`  | `string`            | URL to the artist's Last.fm page.            |
| `artistMbid` | `string`            | MusicBrainz Artist ID.                       |
| `lovedAt`    | `DateTimeImmutable` | When the track was loved.                    |
| `images`     | `list<ImageDto>`    | Track images in various sizes.               |
| `streamable` | `bool`              | Whether the track is streamable.             |

### ImageDto Properties

| Property | Type     | Description                                         |
|----------|----------|-----------------------------------------------------|
| `size`   | `string` | Image size (`"small"`, `"medium"`, `"large"`, etc). |
| `url`    | `string` | URL to the image.                                   |

## Examples

### Basic Usage

```php
$result = $client->user()->getLovedTracks('rj');

foreach ($result->items as $track) {
    echo "{$track->artistName} - {$track->name}\n";
}
```

### Pagination

```php
// Fetch page 3, 10 tracks per page
$result = $client->user()->getLovedTracks('rj', limit: 10, page: 3);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
echo "Total loved tracks: {$result->pagination->total}\n";
```

### Accessing Track Details

```php
$result = $client->user()->getLovedTracks('rj', limit: 5);

foreach ($result->items as $track) {
    echo "{$track->artistName} - {$track->name}\n";
    echo "  Loved at: {$track->lovedAt->format('Y-m-d H:i')}\n";
    echo "  URL: {$track->url}\n";

    foreach ($track->images as $image) {
        echo "  {$image->size}: {$image->url}\n";
    }
}
```

### Iterating All Pages

```php
$page = 1;

do {
    $result = $client->user()->getLovedTracks('rj', limit: 100, page: $page);

    foreach ($result->items as $track) {
        echo "{$track->artistName} - {$track->name}\n";
    }

    $page++;
} while ($page <= $result->pagination->totalPages);
```
