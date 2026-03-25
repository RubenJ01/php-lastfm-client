# user.getWeeklyChartList

Get the list of available weekly chart date ranges for a user.

**Last.fm API Reference:** [user.getWeeklyChartList](https://lastfm-docs.github.io/api-docs/user/getWeeklyChartList/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$ranges = $client->user()->getWeeklyChartList('rj');
```

## Parameters

| Parameter | Type     | Required | Description |
|----------|----------|----------|-------------|
| `$user`   | `string` | Yes      | The username. |

## Return Type

Returns a `list<WeeklyChartRangeDto>` object.

