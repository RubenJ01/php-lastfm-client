# track.getTags

Get the tags applied by an individual user to a track.

**Last.fm API Reference:** [track.getTags](https://lastfm-docs.github.io/api-docs/track/getTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$tags = $client->track()->getTags(
    user: 'RJ',
    artist: 'AC/DC',
    track: 'Hells Bells',
);
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|---------------|-----------|----------|---------|-------------|
| `$user`        | `string`  | Yes      | —       | Username to look up tags for. |
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (required unless using `$mbid`). |
| `$track`       | `?string` | Cond.    | `null`  | Track name (required unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | Track MBID (required unless using `$artist` + `$track`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |

## Return Type

Returns a `list<TrackTagDto>`.

