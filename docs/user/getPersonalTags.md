# user.getPersonalTags

Get the items a user has tagged with a personal tag.

**API Documentation:** [Last.fm - user.getPersonalTags](https://lastfm-docs.github.io/api-docs/user/getPersonalTags/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

// Get artists tagged as "rock"
$result = $client->user()->getPersonalTags('rj', 'rock', 'artist');
foreach ($result->items as $artist) {
    echo "{$artist->name}\n";
}

// Get albums tagged as "electronic"
$result = $client->user()->getPersonalTags('rj', 'electronic', 'album');
foreach ($result->items as $album) {
    echo "{$album->name} by {$album->artistName}\n";
}

// Get tracks tagged as "chill"
$result = $client->user()->getPersonalTags('rj', 'chill', 'track');
foreach ($result->items as $track) {
    echo "{$track->name} by {$track->artistName}\n";
}

// Pagination
echo "Page {$result->pagination->page} of {$result->pagination->totalPages}";
```

## Parameters

| Parameter     | Type   | Required | Default | Description                                  |
|---------------|--------|----------|---------|----------------------------------------------|
| `user`        | string | Yes      | —       | The Last.fm username                         |
| `tag`         | string | Yes      | —       | The tag name                                 |
| `taggingType` | string | Yes      | —       | One of: `artist`, `album`, or `track`        |
| `limit`       | int    | No       | 50      | Number of results per page (max 50)          |
| `page`        | int    | No       | 1       | Page number to fetch                         |

## Response

Returns a `PaginatedResponse` containing items of different types based on `taggingType`.

### PersonalTagArtistDto (taggingType = 'artist')

| Property     | Type             | Description               |
|--------------|------------------|---------------------------|
| `name`       | string           | Artist name               |
| `url`        | string           | Last.fm artist URL        |
| `mbid`       | string           | MusicBrainz ID            |
| `streamable` | bool             | Whether streamable        |
| `images`     | list\<ImageDto\> | Artist images              |

### PersonalTagAlbumDto (taggingType = 'album')

| Property     | Type             | Description               |
|--------------|------------------|---------------------------|
| `name`       | string           | Album name                |
| `url`        | string           | Last.fm album URL         |
| `mbid`       | string           | MusicBrainz ID            |
| `artistName` | string           | Artist name               |
| `artistUrl`  | string           | Last.fm artist URL        |
| `artistMbid` | string           | Artist MusicBrainz ID     |
| `images`     | list\<ImageDto\> | Album images               |

### PersonalTagTrackDto (taggingType = 'track')

| Property     | Type             | Description               |
|--------------|------------------|---------------------------|
| `name`       | string           | Track name                |
| `url`        | string           | Last.fm track URL         |
| `mbid`       | string           | MusicBrainz ID            |
| `duration`   | string           | Track duration             |
| `artistName` | string           | Artist name               |
| `artistUrl`  | string           | Last.fm artist URL        |
| `artistMbid` | string           | Artist MusicBrainz ID     |
| `streamable` | bool             | Whether streamable        |
| `images`     | list\<ImageDto\> | Track images               |

## Error Handling

An `\InvalidArgumentException` is thrown if the `taggingType` is not one of `artist`, `album`, or `track`.
