# user.getTopArtists

Get a paginated list of a user's top artists.

**Last.fm API Reference:** [user.getTopArtists](https://lastfm-docs.github.io/api-docs/user/getTopArtists/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->user()->getTopArtists('rj');
```

## Parameters

| Parameter | Type     | Required | Default     | Description |
|----------|----------|----------|-------------|-------------|
| `$user`   | `string` | Yes      | —           | The username. |
| `$period` | `string` | No       | `"overall"` | One of: `overall`, `7day`, `1month`, `3month`, `6month`, `12month`. |
| `$limit`  | `int`    | No       | `50`        | Results per page. |
| `$page`   | `int`    | No       | `1`         | Page number. |

## Return Type

Returns a `PaginatedResponse<UserTopArtistDto>` object.

