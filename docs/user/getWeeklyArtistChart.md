# user.getWeeklyArtistChart

Get a user's weekly artist chart for a given date range.

**Last.fm API Reference:** [user.getWeeklyArtistChart](https://lastfm-docs.github.io/api-docs/user/getWeeklyArtistChart/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

// Without a range: latest available chart
$items = $client->user()->getWeeklyArtistChart('rj');

// With a range (unix seconds):
$items = $client->user()->getWeeklyArtistChart('rj', from: 1691971200, to: 1692576000);
```

## Parameters

| Parameter | Type   | Required | Default | Description |
|----------|--------|----------|---------|-------------|
| `$user`   | `string` | Yes    | —       | The username. |
| `$from`   | `?int` | No       | `null`  | Range start timestamp (unix seconds). |
| `$to`     | `?int` | No       | `null`  | Range end timestamp (unix seconds). |

## Return Type

Returns a `list<WeeklyArtistChartItemDto>` object.

