# user.getInfo

Get information about a user's profile.

**Last.fm API Reference:** [user.getInfo](https://lastfm-docs.github.io/api-docs/user/getInfo/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$user = $client->user()->getInfo('rj');
```

## Parameters

| Parameter | Type     | Required | Description                        |
|-----------|----------|----------|------------------------------------|
| `$user`   | `string` | Yes      | The username to fetch info for.    |

## Return Type

Returns a `UserDto` object.

### UserDto Properties

| Property       | Type                | Description                                  |
|----------------|---------------------|----------------------------------------------|
| `name`         | `string`            | The user's Last.fm username.                 |
| `realname`     | `string`            | The user's real name.                        |
| `url`          | `string`            | URL to the user's Last.fm profile.           |
| `country`      | `string`            | The user's country.                          |
| `age`          | `int`               | The user's age.                              |
| `subscriber`   | `bool`              | Whether the user is a subscriber.            |
| `playcount`    | `int`               | Total number of scrobbles.                   |
| `artistCount`  | `int`               | Number of unique artists scrobbled.          |
| `trackCount`   | `int`               | Number of unique tracks scrobbled.           |
| `albumCount`   | `int`               | Number of unique albums scrobbled.           |
| `playlists`    | `int`               | Number of playlists.                         |
| `images`       | `list<ImageDto>`    | Profile images in various sizes.             |
| `registered`   | `DateTimeImmutable`  | When the user registered.                    |
| `type`         | `string`            | The user's type (e.g. `"alum"`, `"user"`).   |

### ImageDto Properties

| Property | Type     | Description                                         |
|----------|----------|-----------------------------------------------------|
| `size`   | `string` | Image size (`"small"`, `"medium"`, `"large"`, etc). |
| `url`    | `string` | URL to the image.                                   |

## Example

```php
$user = $client->user()->getInfo('rj');

echo $user->name;                          // "RJ"
echo $user->realname;                      // "Richard Jones"
echo $user->playcount;                     // 150316
echo $user->country;                       // "United Kingdom"
echo $user->subscriber;                    // true
echo $user->registered->format('Y-m-d');   // "2002-11-20"

// Access profile images
foreach ($user->images as $image) {
    echo "{$image->size}: {$image->url}\n";
}
```
