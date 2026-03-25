# user.getTopTracks

Get a paginated list of a user's top tracks.

**Last.fm API Reference:** [user.getTopTracks](https://lastfm-docs.github.io/api-docs/user/getTopTracks/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->user()->getTopTracks('rj');
```

## Parameters

| Parameter | Type     | Required | Default     | Description |
|----------|----------|----------|-------------|-------------|
| `$user`   | `string` | Yes      | —           | The username. |
| `$period` | `string` | No       | `"overall"` | One of: `overall`, `7day`, `1month`, `3month`, `6month`, `12month`. |
| `$limit`  | `int`    | No       | `50`        | Results per page. |
| `$page`   | `int`    | No       | `1`         | Page number. |

## Return Type

Returns a `PaginatedResponse<UserTopTrackDto>` object.

