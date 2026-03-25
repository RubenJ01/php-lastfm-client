# user.getRecentTracks

Get a paginated list of a user's recent tracks (scrobbles).

**Last.fm API Reference:** [user.getRecentTracks](https://lastfm-docs.github.io/api-docs/user/getRecentTracks/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->user()->getRecentTracks('rj');
```

## Parameters

| Parameter   | Type      | Required | Default | Description |
|------------|-----------|----------|---------|-------------|
| `$user`     | `string`  | Yes      | —       | The username to fetch recent tracks for. |
| `$limit`    | `int`     | No       | `50`    | Number of results per page (max 200). |
| `$page`     | `int`     | No       | `1`     | The page number to fetch. |
| `$from`     | `?int`    | No       | `null`  | Start timestamp (unix seconds). |
| `$to`       | `?int`    | No       | `null`  | End timestamp (unix seconds). |
| `$extended` | `bool`    | No       | `false` | Return extended track info (Last.fm API option). |

## Return Type

Returns a `PaginatedResponse<RecentTrackDto>` object.

### RecentTrackDto Properties

| Property       | Type                 | Description |
|----------------|----------------------|-------------|
| `name`         | `string`             | Track name. |
| `url`          | `string`             | Track URL. |
| `mbid`         | `string`             | Track MBID (may be empty). |
| `artistName`   | `string`             | Artist name. |
| `artistMbid`   | `?string`            | Artist MBID (may be missing). |
| `albumName`    | `?string`            | Album name (may be missing). |
| `scrobbledAt`  | `?DateTimeImmutable` | When the track was scrobbled (null when now playing). |
| `nowPlaying`   | `bool`               | True when the track is currently playing. |
| `images`       | `list<ImageDto>`     | Track images in various sizes. |

