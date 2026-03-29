# track.getInfo

Get metadata for a track on Last.fm (by artist+track or by MusicBrainz ID).

**Last.fm API Reference:** [track.getInfo](https://lastfm-docs.github.io/api-docs/track/getInfo/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

// by artist + track
$info = $client->track()->getInfo(artist: 'Linkin Park', track: 'One Step Closer');

// by mbid
$info = $client->track()->getInfo(mbid: '30cb03f3-bd95-43b0-9d41-6d75e13cd353');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|---------------|-----------|----------|---------|-------------|
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (required unless using `$mbid`). |
| `$track`       | `?string` | Cond.    | `null`  | Track name (required unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | Track MBID (required unless using `$artist` + `$track`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |
| `$username`    | `?string` | No       | `null`  | Include user context (user playcount / loved). |

## Return Type

Returns a `TrackInfoDto` object.

