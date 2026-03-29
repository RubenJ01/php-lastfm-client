# artist.getCorrection

Use Last.fm’s correction data to resolve a supplied artist name to the canonical artist.

**Last.fm API Reference:** [artist.getCorrection](https://lastfm-docs.github.io/api-docs/artist/getCorrection/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$correction = $client->artist()->getCorrection('Avici');
```

## Parameters

| Parameter | Type     | Required | Description |
|-----------|----------|----------|-------------|
| `$artist` | `string` | Yes      | Artist name to correct. |

## Return Type

Returns an `ArtistCorrectionDto`.

### ArtistCorrectionDto Properties

| Property | Type                       | Description |
|----------|----------------------------|-------------|
| `artist` | `ArtistCorrectionArtistDto` | Corrected name, MBID, and URL. |
| `index`  | `int`                      | Correction index from `@attr`. |

`ArtistCorrectionArtistDto` exposes `name`, `mbid`, and `url`.

## Errors

If the API returns no usable correction (for example a whitespace-only `corrections` payload), the client throws `RuntimeException` with a short message.

## Examples

```php
try {
    $c = $client->artist()->getCorrection('Avici');
    echo $c->artist->name; // "Avicii"
} catch (\RuntimeException $e) {
    // No correction
}
```
