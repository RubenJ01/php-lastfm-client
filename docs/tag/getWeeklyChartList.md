# tag.getWeeklyChartList

Get weekly chart date ranges available for a tag (Unix timestamps you can use with chart-related calls).

**Last.fm API Reference:** [tag.getWeeklyChartList](https://lastfm-docs.github.io/api-docs/tag/getWeeklyChartList/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$ranges = $client->tag()->getWeeklyChartList('metal');
```

## Parameters

| Parameter | Type     | Required | Description |
|-----------|----------|----------|-------------|
| `$tag`    | `string` | Yes      | The tag name. |

## Return Type

Returns a `list<WeeklyChartRangeDto>` (same DTO as [`user.getWeeklyChartList`](../user/getWeeklyChartList.md)).

### WeeklyChartRangeDto Properties

| Property | Type  | Description |
|----------|-------|-------------|
| `from`   | `int` | Range start (Unix timestamp). |
| `to`     | `int` | Range end (Unix timestamp). |

## Examples

```php
$ranges = $client->tag()->getWeeklyChartList('metal');

foreach ($ranges as $range) {
    echo "{$range->from} → {$range->to}\n";
}
```
