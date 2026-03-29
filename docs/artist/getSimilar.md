# artist.getSimilar

Get artists similar to the given artist (based on listening data).

**Last.fm API Reference:** [artist.getSimilar](https://lastfm-docs.github.io/api-docs/artist/getSimilar/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$similar = $client->artist()->getSimilar('Metallica');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|----------------|-----------|----------|---------|-------------|
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (unless using `$mbid`). |
| `$mbid`        | `?string` | Cond.    | `null`  | MusicBrainz ID (unless using `$artist`). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |
| `$limit`       | `int`     | No       | `30`    | Max similar artists to return. |

## Return Type

Returns `list<SimilarArtistDto>`.

### SimilarArtistDto Properties

| Property     | Type             | Description |
|--------------|------------------|-------------|
| `name`       | `string`         | Artist name. |
| `mbid`       | `string`         | MusicBrainz ID. |
| `match`      | `float`          | Similarity score. |
| `url`        | `string`         | Last.fm URL. |
| `streamable` | `bool`           | Streamable flag. |
| `images`     | `list<ImageDto>` | Images. |

## Examples

```php
foreach ($client->artist()->getSimilar('Metallica', limit: 10) as $a) {
    echo "{$a->name} ({$a->match})\n";
}
```
