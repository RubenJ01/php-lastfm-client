# track.getTopTags

Get the top tags for a track, ordered by tag count.

**Last.fm API Reference:** [track.getTopTags](https://lastfm-docs.github.io/api-docs/track/getTopTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$tags = $client->track()->getTopTags(artist: 'AC/DC', track: 'Hells Bells');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|---------------|-----------|----------|---------|-------------|
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (required unless using `$mbid`). |
| `$track`       | `?string` | Cond.    | `null`  | Track name (required unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | Track MBID (required unless using `$artist` + `$track`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |

## Return Type

Returns a `list<TrackTagDto>`.

