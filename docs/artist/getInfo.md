# artist.getInfo

Get artist metadata: stats, bio, images, similar artists, and tag list.

**Last.fm API Reference:** [artist.getInfo](https://lastfm-docs.github.io/api-docs/artist/getInfo/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$artist = $client->artist()->getInfo('The Weeknd');
```

## Parameters

| Parameter      | Type      | Required | Default | Description |
|----------------|-----------|----------|---------|-------------|
| `$artist`      | `?string` | Cond.    | `null`  | Artist name (required unless `$mbid` is set). |
| `$mbid`        | `?string` | Cond.    | `null`  | MusicBrainz ID (required unless `$artist` is set). |
| `$autocorrect` | `bool`    | No       | `false` | Auto-correct misspelled names. |
| `$username`    | `?string` | No       | `null`  | If set, the API includes that user’s playcount for this artist (`stats.userplaycount`). |
| `$lang`        | `?string` | No       | `null`  | ISO 639-1 language for biography text (API default is English). |

## Return Type

Returns an `ArtistDto` with nested DTOs:

| Property         | Type                      | Description |
|------------------|---------------------------|-------------|
| `name`           | `string`                  | Artist name. |
| `mbid`           | `string`                  | MusicBrainz ID. |
| `url`            | `string`                  | Last.fm URL. |
| `streamable`     | `bool`                    | Streamable flag. |
| `onTour`         | `bool`                    | On tour flag (`ontour`). |
| `stats`          | `ArtistStatsDto`          | `listeners`, `playcount`, optional `userPlaycount`. |
| `bio`            | `?ArtistBioDto`           | `published`, `summary`, `content`. |
| `images`         | `list<ImageDto>`          | Artist images. |
| `similarArtists` | `list<ArtistSummaryDto>`  | `name`, `url`, `images`. |
| `tags`           | `list<ArtistTagDto>`      | `name`, `url` (from the artist’s tag cloud). |

## Examples

```php
$byName = $client->artist()->getInfo('Metallica');
$byMbid = $client->artist()->getInfo(null, 'mbid-here');

$withUser = $client->artist()->getInfo('The Weeknd', username: 'rj');
echo $withUser->stats->userPlaycount;
```
