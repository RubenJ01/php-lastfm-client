# artist.getTags

Get the tags a specific user has applied to an artist.

**Last.fm API Reference:** [artist.getTags](https://lastfm-docs.github.io/api-docs/artist/getTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$tags = $client->artist()->getTags('rj', 'Metallica');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|----------------|-----------|----------|---------|-------------|
| `$user`        | `string`  | Yes      | —       | Last.fm username whose tags to fetch. |
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | Artist MBID (unless using `$artist`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |

## Return Type

Returns `list<TrackTagDto>` (same shape as [`track.getTags`](../track/getTags.md): `name`, `url`, optional `count`).

## Examples

```php
$tags = $client->artist()->getTags('rj', 'Metallica');

foreach ($tags as $tag) {
    echo $tag->name . "\n";
}
```
