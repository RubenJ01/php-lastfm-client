# track.scrobble

Scrobble a track, or a batch of tracks (up to 50), to a user's profile. This is an authenticated write endpoint.

**Last.fm API Reference:** [track.scrobble](https://www.last.fm/api/show/track.scrobble)

## Authentication

This endpoint requires authentication. You must provide an `apiSecret` and `sessionKey` when creating the client:

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient(
    apiKey: 'your-api-key',
    apiSecret: 'your-api-secret',
    sessionKey: 'user-session-key',
);
```

The client automatically generates the required API signature for each authenticated request.

## Usage

### Single Scrobble

```php
use Rjds\PhpLastfmClient\Dto\Track\Scrobble;

$result = $client->track()->scrobble(new Scrobble(
    artist: 'Radiohead',
    track: 'Karma Police',
    timestamp: time(),
));

echo "Accepted: {$result->accepted}, Ignored: {$result->ignored}\n";
```

### Batch Scrobble

```php
$result = $client->track()->scrobbleBatch([
    new Scrobble(
        artist: 'Radiohead',
        track: 'Karma Police',
        timestamp: time() - 600,
        album: 'OK Computer',
    ),
    new Scrobble(
        artist: 'Radiohead',
        track: 'Lucky',
        timestamp: time() - 300,
        album: 'OK Computer',
    ),
]);

echo "Accepted: {$result->accepted}, Ignored: {$result->ignored}\n";
```

## Scrobble Parameters

The `Scrobble` value object accepts the following parameters:

| Parameter      | Type    | Required | Description                                                  |
|----------------|---------|----------|--------------------------------------------------------------|
| `$artist`      | `string` | Yes     | The artist name.                                             |
| `$track`       | `string` | Yes     | The track name.                                              |
| `$timestamp`   | `int`    | Yes     | UNIX timestamp when the track started playing (UTC).         |
| `$album`       | `?string`| No      | The album name.                                              |
| `$albumArtist` | `?string`| No      | The album artist, if different from the track artist.        |
| `$trackNumber` | `?int`   | No      | The track number on the album.                               |
| `$mbid`        | `?string`| No      | The MusicBrainz Track ID.                                    |
| `$duration`    | `?int`   | No      | The track length in seconds.                                 |
| `$chosenByUser`| `?bool`  | No      | `true` if user chose the song, `false` if auto-selected.    |

## Return Type

Both `scrobble()` and `scrobbleBatch()` return a `ScrobbleResponseDto`.

### ScrobbleResponseDto Properties

| Property    | Type                       | Description                     |
|-------------|----------------------------|---------------------------------|
| `accepted`  | `int`                      | Number of accepted scrobbles.   |
| `ignored`   | `int`                      | Number of ignored scrobbles.    |
| `scrobbles` | `list<ScrobbleResultDto>`  | Details for each scrobble.      |

### ScrobbleResultDto Properties

| Property               | Type     | Description                                        |
|------------------------|----------|----------------------------------------------------|
| `track`                | `string` | The track name (possibly corrected).               |
| `trackCorrected`       | `bool`   | Whether the track name was auto-corrected.         |
| `artist`               | `string` | The artist name (possibly corrected).              |
| `artistCorrected`      | `bool`   | Whether the artist name was auto-corrected.        |
| `album`                | `string` | The album name (possibly corrected).               |
| `albumCorrected`       | `bool`   | Whether the album name was auto-corrected.         |
| `albumArtist`          | `string` | The album artist name (possibly corrected).        |
| `albumArtistCorrected` | `bool`   | Whether the album artist was auto-corrected.       |
| `timestamp`            | `int`    | The scrobble timestamp.                            |
| `ignoredCode`          | `int`    | Ignored reason code (0 = not ignored).             |
| `ignoredMessage`       | `string` | Human-readable ignored reason.                     |

### Ignored Codes

| Code | Meaning                      |
|------|------------------------------|
| 0    | Not ignored (accepted).      |
| 1    | Artist was ignored.          |
| 2    | Track was ignored.           |
| 3    | Timestamp was too old.       |
| 4    | Timestamp was too new.       |
| 5    | Daily scrobble limit exceeded.|

## Examples

### Checking Scrobble Results

```php
$result = $client->track()->scrobble(new Scrobble(
    artist: 'Radiohead',
    track: 'Karma Police',
    timestamp: time(),
));

foreach ($result->scrobbles as $scrobble) {
    if ($scrobble->ignoredCode > 0) {
        echo "Ignored: {$scrobble->ignoredMessage}\n";
        continue;
    }

    echo "Scrobbled: {$scrobble->artist} - {$scrobble->track}\n";

    if ($scrobble->artistCorrected) {
        echo "  (artist name was corrected)\n";
    }
}
```

### Full Scrobble with All Options

```php
$result = $client->track()->scrobble(new Scrobble(
    artist: 'Radiohead',
    track: 'Karma Police',
    timestamp: time(),
    album: 'OK Computer',
    albumArtist: 'Radiohead',
    trackNumber: 6,
    duration: 264,
    chosenByUser: true,
));
```
