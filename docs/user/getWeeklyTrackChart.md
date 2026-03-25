# user.getWeeklyTrackChart

Get a user's weekly track chart for a given date range.

**Last.fm API Reference:** [user.getWeeklyTrackChart](https://lastfm-docs.github.io/api-docs/user/getWeeklyTrackChart/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$items = $client->user()->getWeeklyTrackChart('rj');
```

## Parameters

| Parameter | Type     | Required | Default | Description |
|----------|----------|----------|---------|-------------|
| `$user`   | `string` | Yes      | —       | The username. |
| `$from`   | `?int`   | No       | `null`  | Range start timestamp (unix seconds). |
| `$to`     | `?int`   | No       | `null`  | Range end timestamp (unix seconds). |

## Return Type

Returns a `list<WeeklyTrackChartItemDto>` object.

