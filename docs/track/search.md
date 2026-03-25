# track.search

Search for tracks by name (optionally narrowed by artist).

**Last.fm API Reference:** [track.search](https://lastfm-docs.github.io/api-docs/track/search/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->track()->search('Hells Bells');
```

## Parameters

| Parameter | Type      | Required | Default | Description |
|----------|-----------|----------|---------|-------------|
| `$track`  | `string`  | Yes      | —       | Track name to search for. |
| `$artist` | `?string` | No       | `null`  | Optional artist filter. |
| `$limit`  | `int`     | No       | `30`    | Results per page. |
| `$page`   | `int`     | No       | `1`     | Page number. |

## Return Type

Returns a `PaginatedResponse<TrackSearchResultDto>` object.

