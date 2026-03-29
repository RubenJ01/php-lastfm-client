# artist.getTopTags

Get the top community tags for an artist (by tag count).

**Last.fm API Reference:** [artist.getTopTags](https://lastfm-docs.github.io/api-docs/artist/getTopTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$tags = $client->artist()->getTopTags('Metallica');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|----------------|-----------|----------|---------|-------------|
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | Artist MBID (unless using `$artist`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |

## Return Type

Returns `list<UserTopTagDto>` (`name`, `url`, `count`), same as other “top tags” list endpoints in this library.

## Examples

```php
foreach ($client->artist()->getTopTags('Metallica') as $tag) {
    echo "{$tag->name}: {$tag->count}\n";
}
```
