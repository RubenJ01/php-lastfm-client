# tag.getSimilar

Get tags similar to a given tag (ranked by listening data).

**Last.fm API Reference:** [tag.getSimilar](https://lastfm-docs.github.io/api-docs/tag/getSimilar/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->tag()->getSimilar('metal');
```

## Parameters

| Parameter | Type     | Required | Description |
|-----------|----------|----------|-------------|
| `$tag`    | `string` | Yes      | The tag name. |

## Return Type

Returns a `TagSimilarTagsResult` object.

### TagSimilarTagsResult Properties

| Property     | Type                   | Description |
|--------------|------------------------|-------------|
| `sourceTag`  | `?string`              | The tag name echoed by the API in `@attr`, or `null` when the API returns `n/a`. |
| `tags`       | `list<TagSimilarDto>`  | Similar tags. |

### TagSimilarDto Properties

| Property     | Type      | Description |
|--------------|-----------|-------------|
| `name`       | `string`  | Similar tag name. |
| `url`        | `string`  | Last.fm tag URL. |
| `streamable` | `bool`    | Streamable flag from the API. |
| `match`      | `?string` | Similarity score when present. |

## Examples

```php
$result = $client->tag()->getSimilar('metal');

foreach ($result->tags as $similar) {
    echo $similar->name . "\n";
}
```
