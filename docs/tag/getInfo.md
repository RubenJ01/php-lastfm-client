# tag.getInfo

Get metadata for a tag (name, usage totals, wiki summary).

**Last.fm API Reference:** [tag.getInfo](https://lastfm-docs.github.io/api-docs/tag/getInfo/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$info = $client->tag()->getInfo('metal');
```

## Parameters

| Parameter | Type      | Required | Default | Description |
|-----------|-----------|----------|---------|-------------|
| `$tag`    | `string`  | Yes      | —       | The tag name. |
| `$lang`   | `?string` | No       | —       | ISO 639-1 language code for wiki text (API default is English). Omit to use the API default. |

## Return Type

Returns a `TagInfoDto` object.

### TagInfoDto Properties

| Property | Type         | Description |
|----------|--------------|-------------|
| `name`   | `string`     | Tag name. |
| `total`  | `int`        | Total taggings. |
| `reach`  | `int`        | Number of unique users who have used this tag. |
| `wiki`   | `TagWikiDto` | Wiki summary and content. |

### TagWikiDto Properties

| Property  | Type     | Description |
|-----------|----------|-------------|
| `summary` | `string` | Short HTML summary. |
| `content` | `string` | Full wiki body. |

## Examples

```php
$info = $client->tag()->getInfo('metal');
echo $info->name . ': ' . $info->total . " taggings\n";
echo $info->wiki->summary;

$de = $client->tag()->getInfo('metal', 'de');
```
