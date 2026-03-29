# API Reference

All available endpoints grouped by service. Click on a method name to view full documentation with usage examples, parameters, and response DTOs.

## Auth

| Method         | Description                              |
|----------------|------------------------------------------|
| `getToken`     | Get a request token for authentication   |
| `getSession`   | Exchange an authorized token for session |

See the [authentication guide](auth/authentication.md) for the full flow.

## Artist

| Method                                            | Description                                |
|---------------------------------------------------|--------------------------------------------|
| [`getCorrection`](artist/getCorrection.md)        | Correct a misspelled artist name           |
| [`getInfo`](artist/getInfo.md)                    | Get artist metadata, bio, similar, tags    |
| [`getSimilar`](artist/getSimilar.md)              | Get similar artists                        |
| [`getTags`](artist/getTags.md)                    | Get tags a user applied to an artist       |
| [`getTopAlbums`](artist/getTopAlbums.md)          | Get an artist’s top albums                 |
| [`getTopTags`](artist/getTopTags.md)              | Get top community tags for an artist       |
| [`getTopTracks`](artist/getTopTracks.md)          | Get an artist’s top tracks                 |
| [`search`](artist/search.md)                      | Search for artists by name                 |

## Chart

| Method                                       | Description                |
|----------------------------------------------|----------------------------|
| [`getTopArtists`](chart/getTopArtists.md)    | Get the top artists chart  |
| [`getTopTags`](chart/getTopTags.md)          | Get the top tags chart     |
| [`getTopTracks`](chart/getTopTracks.md)      | Get the top tracks chart   |

## Geo

| Method                                       | Description                      |
|----------------------------------------------|----------------------------------|
| [`getTopArtists`](geo/getTopArtists.md)      | Get the top artists by country   |
| [`getTopTracks`](geo/getTopTracks.md)        | Get the top tracks by country    |

## Library

| Method                                       | Description                          |
|----------------------------------------------|--------------------------------------|
| [`getArtists`](library/getArtists.md)        | Get all artists in a user's library  |

## Tag

| Method                                            | Description                              |
|---------------------------------------------------|------------------------------------------|
| [`getInfo`](tag/getInfo.md)                       | Get metadata and wiki for a tag          |
| [`getSimilar`](tag/getSimilar.md)               | Get tags similar to a tag                |
| [`getTopAlbums`](tag/getTopAlbums.md)             | Get top albums for a tag                 |
| [`getTopArtists`](tag/getTopArtists.md)           | Get top artists for a tag                |
| [`getTopTags`](tag/getTopTags.md)                 | Get global top tags (by popularity)      |
| [`getTopTracks`](tag/getTopTracks.md)             | Get top tracks for a tag                   |
| [`getWeeklyChartList`](tag/getWeeklyChartList.md) | Get weekly chart date ranges for a tag   |

## Track

| Method                                       | Description                          |
|----------------------------------------------|--------------------------------------|
| [`scrobble`](track/scrobble.md)              | Scrobble a track to a user's profile |
| [`getCorrection`](track/getCorrection.md)    | Get correction for a track           |
| [`getInfo`](track/getInfo.md)                | Get track metadata                   |
| [`getSimilar`](track/getSimilar.md)          | Get similar tracks                   |
| [`getTags`](track/getTags.md)                | Get user-applied track tags          |
| [`getTopTags`](track/getTopTags.md)          | Get top tags for a track             |
| [`search`](track/search.md)                  | Search for tracks                    |

## User

| Method                                            | Description                      |
|---------------------------------------------------|----------------------------------|
| [`getInfo`](user/getInfo.md)                      | Get information about a user     |
| [`getFriends`](user/getFriends.md)                | Get a user's friends             |
| [`getLovedTracks`](user/getLovedTracks.md)        | Get a user's loved tracks        |
| [`getPersonalTags`](user/getPersonalTags.md)      | Get items a user has tagged      |
| [`getRecentTracks`](user/getRecentTracks.md)      | Get a user's recent tracks       |
| [`getTopAlbums`](user/getTopAlbums.md)            | Get a user's top albums          |
| [`getTopArtists`](user/getTopArtists.md)          | Get a user's top artists         |
| [`getTopTags`](user/getTopTags.md)                | Get a user's top tags            |
| [`getTopTracks`](user/getTopTracks.md)            | Get a user's top tracks          |
| [`getWeeklyAlbumChart`](user/getWeeklyAlbumChart.md) | Get a weekly album chart      |
| [`getWeeklyArtistChart`](user/getWeeklyArtistChart.md) | Get a weekly artist chart    |
| [`getWeeklyChartList`](user/getWeeklyChartList.md) | Get weekly chart date ranges   |
| [`getWeeklyTrackChart`](user/getWeeklyTrackChart.md) | Get a weekly track chart      |
