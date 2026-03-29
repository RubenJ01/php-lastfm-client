# tag.getTopTags

Fetch global top tags on Last.fm, sorted by popularity (how often they are used).

This is different from [`chart.getTopTags`](../chart/getTopTags.md), which returns the chart service’s paginated tag chart with `ChartTagDto` items. The tag service returns **`TagTopTagsResult`** with offset-based metadata (`offset`, `numRes`, `total`), not `PaginationDto` page numbers.

**Last.fm API Reference:** [tag.getTopTags](https://lastfm-docs.github.io/api-docs/tag/getTopTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->tag()->getTopTags();
```

## Parameters

| Parameter  | Type     | Required | Default | Description |
|------------|----------|----------|---------|-------------|
| `$limit`   | `?int`   | No       | —       | Pass to limit results when supported by the API. |
| `$offset`  | `?int`   | No       | —       | Pass to skip results when supported by the API. |

Omit `$limit` and `$offset` to use the API defaults.

## Return Type

Returns a `TagTopTagsResult` object.

### TagTopTagsResult Properties

| Property     | Type                      | Description |
|--------------|---------------------------|-------------|
| `tags`       | `list<TagGlobalTopTagDto>` | Global tags for this response. |
| `pagination` | `TagTopTagsPaginationDto` | Offset-style metadata (`offset`, `numRes`, `total`). |

### TagGlobalTopTagDto Properties

| Property | Type    | Description |
|----------|---------|-------------|
| `name`   | `string` | Tag name. |
| `count`  | `int`    | Number of times the tag has been used. |
| `reach`  | `int`    | Reach metric from the API. |

### TagTopTagsPaginationDto Properties

| Property | Type  | Description |
|----------|-------|-------------|
| `offset` | `int` | Starting offset. |
| `numRes` | `int` | Number of results in this response (maps from `num_res`). |
| `total`  | `int` | Total tags available. |

## Examples

```php
$result = $client->tag()->getTopTags();

foreach ($result->tags as $tag) {
    echo "{$tag->name}: {$tag->count} uses\n";
}

echo "Showing {$result->pagination->numRes} of {$result->pagination->total}\n";
```
