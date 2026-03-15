# chart.getTopTags

Get the top tags chart.

**Last.fm API Reference:** [chart.getTopTags](https://lastfm-docs.github.io/api-docs/chart/getTopTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->chart()->getTopTags();
```

## Parameters

| Parameter | Type  | Required | Default | Description                    |
|-----------|-------|----------|---------|--------------------------------|
| `$limit`  | `int` | No       | `50`    | Number of results per page.    |
| `$page`   | `int` | No       | `1`     | The page number to fetch.      |

## Return Type

Returns a `PaginatedResponse<ChartTagDto>` object.

### PaginatedResponse Properties

| Property     | Type                   | Description                    |
|--------------|------------------------|--------------------------------|
| `items`      | `list<ChartTagDto>`    | The tags on this page.         |
| `pagination` | `PaginationDto`        | Pagination metadata.           |

### ChartTagDto Properties

| Property     | Type     | Description                                      |
|--------------|----------|--------------------------------------------------|
| `name`       | `string` | The tag name.                                    |
| `url`        | `string` | URL to the tag's Last.fm page.                   |
| `reach`      | `int`    | Number of unique artists tagged with this tag.   |
| `taggings`   | `int`    | Total number of times this tag has been applied. |
| `streamable` | `bool`   | Whether the tag is streamable.                   |

## Examples

### Basic Usage

```php
$result = $client->chart()->getTopTags();

foreach ($result->items as $tag) {
    echo "{$tag->name}: {$tag->taggings} taggings, {$tag->reach} reach\n";
}
```

### Pagination

```php
$result = $client->chart()->getTopTags(limit: 10, page: 2);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
```
