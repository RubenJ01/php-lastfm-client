# library.getArtists

Get a paginated list of all the artists in a user's library, with play counts and tag counts.

**Last.fm API Reference:** [library.getArtists](https://lastfm-docs.github.io/api-docs/library/getArtists/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->library()->getArtists('rj');
```

## Parameters

| Parameter | Type     | Required | Default | Description                              |
|-----------|----------|----------|---------|------------------------------------------|
| `$user`   | `string` | Yes      | —       | The user whose library to fetch.         |
| `$limit`  | `int`    | No       | `50`    | Number of results per page (max 2000).   |
| `$page`   | `int`    | No       | `1`     | The page number to fetch.                |

## Return Type

Returns a `PaginatedResponse<LibraryArtistDto>` object.

### PaginatedResponse Properties

| Property     | Type                     | Description                    |
|--------------|--------------------------|--------------------------------|
| `items`      | `list<LibraryArtistDto>` | The artists on this page.      |
| `pagination` | `PaginationDto`          | Pagination metadata.           |

### PaginationDto Properties

| Property     | Type  | Description                      |
|--------------|-------|----------------------------------|
| `page`       | `int` | The current page number.         |
| `perPage`    | `int` | Number of results per page.      |
| `total`      | `int` | Total number of results.         |
| `totalPages` | `int` | Total number of pages.           |

### LibraryArtistDto Properties

| Property     | Type             | Description                              |
|--------------|------------------|------------------------------------------|
| `name`       | `string`         | The artist's name.                       |
| `url`        | `string`         | URL to the artist's Last.fm page.        |
| `mbid`       | `string`         | MusicBrainz ID.                          |
| `tagcount`   | `int`            | Number of tags the user has applied.     |
| `playcount`  | `int`            | Number of times the user played this artist. |
| `streamable` | `bool`           | Whether the artist is streamable.        |
| `images`     | `list<ImageDto>` | Artist images in various sizes.          |

### ImageDto Properties

| Property | Type     | Description                                         |
|----------|----------|-----------------------------------------------------|
| `size`   | `string` | Image size (`"small"`, `"medium"`, `"large"`, etc). |
| `url`    | `string` | URL to the image.                                   |

## Examples

### Basic Usage

```php
$result = $client->library()->getArtists('rj');

foreach ($result->items as $artist) {
    echo "{$artist->name}: {$artist->playcount} plays\n";
}
```

### Pagination

```php
// Fetch page 3, 10 artists per page
$result = $client->library()->getArtists('rj', limit: 10, page: 3);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
echo "Total artists: {$result->pagination->total}\n";
```

### Iterating All Pages

```php
$page = 1;

do {
    $result = $client->library()->getArtists('rj', limit: 100, page: $page);

    foreach ($result->items as $artist) {
        echo "{$artist->name}: {$artist->playcount} plays\n";
    }

    $page++;
} while ($page <= $result->pagination->totalPages);
```

### Accessing Artist Images

```php
$result = $client->library()->getArtists('rj', limit: 5);

foreach ($result->items as $artist) {
    echo "{$artist->name}\n";
    foreach ($artist->images as $image) {
        echo "  {$image->size}: {$image->url}\n";
    }
}
```
