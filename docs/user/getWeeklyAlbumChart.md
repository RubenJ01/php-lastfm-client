# user.getWeeklyAlbumChart

Get a user's weekly album chart for a given date range.

**Last.fm API Reference:** [user.getWeeklyAlbumChart](https://lastfm-docs.github.io/api-docs/user/getWeeklyAlbumChart/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$items = $client->user()->getWeeklyAlbumChart('rj');
```

## Parameters

| Parameter | Type     | Required | Default | Description |
|----------|----------|----------|---------|-------------|
| `$user`   | `string` | Yes      | —       | The username. |
| `$from`   | `?int`   | No       | `null`  | Range start timestamp (unix seconds). |
| `$to`     | `?int`   | No       | `null`  | Range end timestamp (unix seconds). |

## Return Type

Returns a `list<WeeklyAlbumChartItemDto>` object.

