# artist.getTopAlbums

Get an artist’s top albums on Last.fm, ordered by popularity.

**Last.fm API Reference:** [artist.getTopAlbums](https://lastfm-docs.github.io/api-docs/artist/getTopAlbums/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->artist()->getTopAlbums('Soundgarden');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|----------------|-----------|----------|---------|-------------|
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | Artist MBID (unless using `$artist`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |
| `$limit`       | `int`     | No       | `50`    | Results per page. |
| `$page`        | `int`     | No       | `1`     | Page number. |

## Return Type

Returns `PaginatedResponse<UserTopAlbumDto>` (same DTO as [`user.getTopAlbums`](../user/getTopAlbums.md)).

## Examples

```php
$result = $client->artist()->getTopAlbums('Soundgarden', limit: 10);

foreach ($result->items as $album) {
    echo "{$album->name} — {$album->playcount} plays\n";
}
```
