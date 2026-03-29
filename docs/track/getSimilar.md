# track.getSimilar

Get similar tracks for a given track (based on listening data).

**Last.fm API Reference:** [track.getSimilar](https://lastfm-docs.github.io/api-docs/track/getSimilar/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$tracks = $client->track()->getSimilar(artist: 'Disturbed', track: 'Stricken');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|---------------|-----------|----------|---------|-------------|
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (required unless using `$mbid`). |
| `$track`       | `?string` | Cond.    | `null`  | Track name (required unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | Track MBID (required unless using `$artist` + `$track`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |
| `$limit`       | `int`     | No       | `100`   | Number of results to return. |

## Return Type

Returns a `list<SimilarTrackDto>`.

