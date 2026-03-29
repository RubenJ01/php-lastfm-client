# artist.getTopTracks

Get an artist’s top tracks on Last.fm, ordered by popularity.

**Last.fm API Reference:** [artist.getTopTracks](https://lastfm-docs.github.io/api-docs/artist/getTopTracks/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->artist()->getTopTracks('Metallica');
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

Returns `PaginatedResponse<UserTopTrackDto>` (same DTO as [`user.getTopTracks`](../user/getTopTracks.md)).

## Examples

```php
$result = $client->artist()->getTopTracks('Metallica', limit: 20);

foreach ($result->items as $track) {
    echo "{$track->name} — {$track->playcount} plays\n";
}
```
