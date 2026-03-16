# user.getFriends

Get a paginated list of a users friends.

**Last.fm API Reference:** [user.getLovedTracks](https://lastfm-docs.github.io/api-docs/user/getFriends/)

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

$result = $client->user()->getFriends('RubenJ01');
```

## Parameters

| Parameter | Type     | Required | Default | Description                            |
|-----------|----------|----------|---------|----------------------------------------|
| `$user`   | `string` | Yes      | —       | The username to fetch friends for.     |
| `$limit`  | `int`    | No       | `50`    | Number of results per page (max 1000). |
| `$page`   | `int`    | No       | `1`     | The page number to fetch.              |

## Return Type

Returns a `PaginatedResponse<FriendDto>` object.

### PaginatedResponse Properties

| Property     | Type                    | Description                   |
|--------------|-------------------------|-------------------------------|
| `items`      | `list<LovedTrackDto>`   | The loved tracks on this page.|
| `pagination` | `PaginationDto`         | Pagination metadata.          |

### PaginationDto Properties

| Property     | Type  | Description                      |
|--------------|-------|----------------------------------|
| `page`       | `int` | The current page number.         |
| `perPage`    | `int` | Number of results per page.      |
| `total`      | `int` | Total number of results.         |
| `totalPages` | `int` | Total number of pages.           |

### LovedTrackDto Properties

| Property     | Type                | Description                                     |
|--------------|---------------------|-------------------------------------------------|
| `name`       | `string`            | The friends name                                |
| `realname`   | `string`            | The friends full name.                          |
| `country`    | `string`            | The friends country of origin.                  |
| `url`        | `string`            | URL to the friends Last.fm page.                |
| `playlist`   | `int`               | The amount of playlists this friend has.        |
| `playcount`  | `int`               | The playcount this friend has.                  |
| `subscriber` | `bool`              | Whether the friend is subscribed to LastFM pro. |
| `images`     | `list<ImageDto>`    | Friends images in various sizes.                |
| `registered` | `DateTimeImmutable` | When this friend was registered.                |
| `type`       | `DateTimeImmutable` | The type of user this friend is.                |
| `bootstrap`  | `bool`              | ...                                             |

### ImageDto Properties

| Property | Type     | Description                                         |
|----------|----------|-----------------------------------------------------|
| `size`   | `string` | Image size (`"small"`, `"medium"`, `"large"`, etc). |
| `url`    | `string` | URL to the image.                                   |

## Examples

### Basic Usage

```php
$result = $client->user()->getFriends('RubenJ01');

foreach ($result->items as $user) {
    echo $user->name . "\n";
}
```

### Pagination

```php
// Fetch page 3, 4 friends per page
$result = $client->user()->getFriends('RubenJ01', limit: 4, page: 3);

echo "Page {$result->pagination->page} of {$result->pagination->totalPages}\n";
echo "Total friends: {$result->pagination->total}\n";
```

### Accessing Friend Details

```php
$result = $client->user()->getFriends('RubenJ01', limit: 5);

foreach ($result->items as $user) {
    echo $user->name . " registered at: " . $user->registered->format('Y-m-d H:i') . "\n";
}
```

### Iterating All Pages

```php
$page = 1;

do {
    $result = $client->user()->getFriends('RubenJ01', limit: 2, page: $page);

    foreach ($result->items as $user) {
        echo $user->name . "\n";
    }

    $page++;
} while ($page <= $result->pagination->totalPages);
```
