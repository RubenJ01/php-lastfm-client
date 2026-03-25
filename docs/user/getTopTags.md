# user.getTopTags

Get a paginated list of a user's top tags.

**Last.fm API Reference:** [user.getTopTags](https://lastfm-docs.github.io/api-docs/user/getTopTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->user()->getTopTags('rj');
```

## Parameters

| Parameter | Type     | Required | Default | Description |
|----------|----------|----------|---------|-------------|
| `$user`   | `string` | Yes      | —       | The username. |
| `$limit`  | `int`    | No       | `50`    | Results per page. |
| `$page`   | `int`    | No       | `1`     | Page number. |

## Return Type

Returns a `PaginatedResponse<UserTopTagDto>` object.

