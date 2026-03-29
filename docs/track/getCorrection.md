# track.getCorrection

Use the Last.fm corrections data to check whether a supplied track has a correction to a canonical track.

**Last.fm API Reference:** [track.getCorrection](https://lastfm-docs.github.io/api-docs/track/getCorrection/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$correction = $client->track()->getCorrection('Skee-Lo', 'I wish');
```

## Parameters

| Parameter | Type     | Required | Description |
|----------|----------|----------|-------------|
| `$artist` | `string` | Yes      | The artist name to correct. |
| `$track`  | `string` | Yes      | The track name to correct. |

## Return Type

Returns a `TrackCorrectionDto` object.

