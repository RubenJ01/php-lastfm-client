# Changelog

## 1.0.0 (2026-03-29)


### Features

* add HasToArray trait for recursive DTO-to-array conversion ([#81](https://github.com/RubenJ01/php-lastfm-client/issues/81)) ([8db5ffc](https://github.com/RubenJ01/php-lastfm-client/commit/8db5ffcbcb4486062973820faaab07732c5d7bd4))
* add HasToString trait for human-readable DTO output ([#80](https://github.com/RubenJ01/php-lastfm-client/issues/80)) ([7f86c31](https://github.com/RubenJ01/php-lastfm-client/commit/7f86c31d763a3ec0559140657dddd4883ec06d3b))
* **http:** add injectable HttpTransportInterface ([#87](https://github.com/RubenJ01/php-lastfm-client/issues/87)) ([83a7fed](https://github.com/RubenJ01/php-lastfm-client/commit/83a7fed1bba05183547d1b0e56133103ac7b503e))
* implement authentication methods (auth.getToken, auth.getSession) ([#13](https://github.com/RubenJ01/php-lastfm-client/issues/13)) ([040f5c1](https://github.com/RubenJ01/php-lastfm-client/commit/040f5c10547841182853b859081073cfbcc8ad76))
* implement chart service (getTopArtists, getTopTags, getTopTracks) ([#75](https://github.com/RubenJ01/php-lastfm-client/issues/75)) ([e1b426e](https://github.com/RubenJ01/php-lastfm-client/commit/e1b426eff2d420f487bd256ab5aea2595009f9ee))
* implement geo service (getTopArtists, getTopTracks) ([#76](https://github.com/RubenJ01/php-lastfm-client/issues/76)) ([c096ea8](https://github.com/RubenJ01/php-lastfm-client/commit/c096ea863dfb25a0a7d4b021b2109982a12375f7))
* implement library.getArtists endpoint ([#7](https://github.com/RubenJ01/php-lastfm-client/issues/7)) ([ecb1557](https://github.com/RubenJ01/php-lastfm-client/commit/ecb1557ee65b7a486fc9659a2671eed6c6a21d89))
* implement track.scrobble endpoint ([#11](https://github.com/RubenJ01/php-lastfm-client/issues/11)) ([140da93](https://github.com/RubenJ01/php-lastfm-client/commit/140da93dc051e75e5d6967ab37bc2485748d5292))
* implement user.getInfo endpoint ([518756f](https://github.com/RubenJ01/php-lastfm-client/commit/518756f89206f15edf524d961b672d21b727fc72))
* implement user.getInfo endpoint ([8a211e5](https://github.com/RubenJ01/php-lastfm-client/commit/8a211e54bf43152196bda80c979bac9ae17dd07a)), closes [#2](https://github.com/RubenJ01/php-lastfm-client/issues/2)
* implement user.getLovedTracks endpoint ([#21](https://github.com/RubenJ01/php-lastfm-client/issues/21)) ([4c5c709](https://github.com/RubenJ01/php-lastfm-client/commit/4c5c709c8dce104cc5085aef6ddc9b2de63a4d72))
* implement user.getPersonalTags endpoint ([#78](https://github.com/RubenJ01/php-lastfm-client/issues/78)) ([72b773d](https://github.com/RubenJ01/php-lastfm-client/commit/72b773dedb2c3c45a4c03552cd43017ac9c97833))
* implemented the user.getFriends endpoint ([#77](https://github.com/RubenJ01/php-lastfm-client/issues/77)) ([9a8cfb3](https://github.com/RubenJ01/php-lastfm-client/commit/9a8cfb38d17f13efbcbb640114c1dcc94f15dbcb))
* initial setup ([faded07](https://github.com/RubenJ01/php-lastfm-client/commit/faded07ce5f98461866ff37e8aa95187e54ee3a7))
* **tag:** implemented all tag endpoints ([#88](https://github.com/RubenJ01/php-lastfm-client/issues/88)) ([88de9d8](https://github.com/RubenJ01/php-lastfm-client/commit/88de9d80b4d4ddc210b719a3c8412d3d2a9dfc4c))
* **track:** implemented remaining track endpoints ([#84](https://github.com/RubenJ01/php-lastfm-client/issues/84)) ([a6ac860](https://github.com/RubenJ01/php-lastfm-client/commit/a6ac8608a241a445f8f86cc597f4c0cf606bb798))
* **user:** implement remaining user endpoints ([#82](https://github.com/RubenJ01/php-lastfm-client/issues/82)) ([faa0e5c](https://github.com/RubenJ01/php-lastfm-client/commit/faa0e5ceaa3a2b73ed238bdf255ca3f6a22cc36e))


### Bug Fixes

* eliminate equivalent CastString mutants in LibraryService ([#19](https://github.com/RubenJ01/php-lastfm-client/issues/19)) ([20b5554](https://github.com/RubenJ01/php-lastfm-client/commit/20b555409173709f1258f7952af10539a84b1997))

## Changelog
