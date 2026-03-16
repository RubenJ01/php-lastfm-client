# API Reference

All available endpoints grouped by service. Click on a method name to view full documentation with usage examples, parameters, and response DTOs.

## Auth

| Method         | Description                              |
|----------------|------------------------------------------|
| `getToken`     | Get a request token for authentication   |
| `getSession`   | Exchange an authorized token for session |

See the [authentication guide](auth/authentication.md) for the full flow.

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

## Track

| Method                                       | Description                          |
|----------------------------------------------|--------------------------------------|
| [`scrobble`](track/scrobble.md)              | Scrobble a track to a user's profile |

## User

| Method                                            | Description                      |
|---------------------------------------------------|----------------------------------|
| [`getInfo`](user/getInfo.md)                      | Get information about a user     |
| [`getFriends`](user/getFriends.md)                | Get a user's friends             |
| [`getLovedTracks`](user/getLovedTracks.md)        | Get a user's loved tracks        |
| [`getPersonalTags`](user/getPersonalTags.md)      | Get items a user has tagged      |
