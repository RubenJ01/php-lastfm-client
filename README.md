# PHP Last.fm Client

A PHP client library for the [Last.fm API](https://www.last.fm/api).

## Requirements

- PHP >= 8.4

## Installation

```bash
composer require rjds/php-lastfm-client
```

## Quick Start

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

// Get user info
$user = $client->user()->getInfo('rj');
echo $user->name;       // "RJ"
echo $user->playcount;  // 150316

// Get library artists (paginated)
$result = $client->library()->getArtists('rj', limit: 10);
foreach ($result->items as $artist) {
    echo "{$artist->name}: {$artist->playcount} plays\n";
}
echo "Page {$result->pagination->page} of {$result->pagination->totalPages}";
```

## Available Endpoints

| Service   | Method         | Description                              | Docs                                     |
|-----------|----------------|------------------------------------------|------------------------------------------|
| `user`    | `getInfo`      | Get information about a user profile     | [View](docs/user/getInfo.md)             |
| `library` | `getArtists`   | Get all artists in a user's library      | [View](docs/library/getArtists.md)       |
| `track`   | `scrobble`     | Scrobble a track to a user's profile     | [View](docs/track/scrobble.md)           |

## Error Handling

The client throws `LastfmApiException` for API errors, which includes the Last.fm [error code](https://lastfm-docs.github.io/api-docs/#error-codes):

```php
use Rjds\PhpLastfmClient\Exception\LastfmApiException;

try {
    $user = $client->user()->getInfo('nonexistent-user');
} catch (LastfmApiException $e) {
    echo $e->getMessage();       // "User not found"
    echo $e->getApiErrorCode();  // 6
}
```

## Custom HTTP Client

By default, the client uses `file_get_contents`. You can provide your own HTTP client by implementing `HttpClientInterface`:

```php
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

class GuzzleHttpClient implements HttpClientInterface
{
    public function get(string $url): string
    {
        // Your Guzzle implementation
    }
}

$client = new LastfmClient('your-api-key', new GuzzleHttpClient());
```

## Compatibility

Tested against the following PHP versions:

- PHP 8.4
- PHP 8.5

## Development

```bash
composer install
php vendor/bin/grumphp run
```

Run mutation testing:

```bash
php vendor/bin/infection --threads=4
```

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full workflow.

## License

MIT
