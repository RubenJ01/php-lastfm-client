# PHP Last.fm Client

![Version](https://img.shields.io/github/v/release/RubenJ01/php-lastfm-client?label=version)
[![codecov](https://codecov.io/github/RubenJ01/php-lastfm-client/graph/badge.svg)](https://codecov.io/github/RubenJ01/php-lastfm-client)
[![status-badge](https://ci.rubenjakob.com/api/badges/RubenJ01/php-lastfm-client/status.svg)](https://ci.rubenjakob.com/repos/RubenJ01/php-lastfm-client)
![License](https://img.shields.io/github/license/RubenJ01/php-lastfm-client)

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

## Available Services

- **[Auth](docs/auth/authentication.md)** — token & session authentication
- **[Chart](docs/api-reference.md#chart)** — top artists, tags, and tracks charts
- **[Geo](docs/api-reference.md#geo)** — top artists and tracks by country
- **[Library](docs/api-reference.md#library)** — user library artists
- **[Tag](docs/api-reference.md#tag)** — tag metadata, similar tags, top albums/artists/tracks, global top tags, weekly chart ranges
- **[Track](docs/api-reference.md#track)** — scrobbling
- **[User](docs/api-reference.md#user)** — user info, friends, loved tracks, personal tags

See the [full API reference](docs/api-reference.md) for all endpoints and parameters.

## Authentication

For write methods (scrobbling, etc.), you need to authenticate. See the [authentication guide](docs/auth/authentication.md) for the full flow:

```php
$client = new LastfmClient(
    apiKey: 'your-api-key',
    apiSecret: 'your-api-secret',
);

$token = $client->auth()->getToken();
$authUrl = $client->auth()->getAuthUrl($token);
// User visits $authUrl and grants access...
$session = $client->auth()->getSession($token);
// Use $session->key for authenticated calls
```

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
        // Your Guzzle GET implementation
    }

    public function post(string $url, array $data): string
    {
        // Your Guzzle POST implementation
    }
}

$client = new LastfmClient('your-api-key', httpClient: new GuzzleHttpClient());
```

## Development

```bash
composer install
php vendor/bin/grumphp run
```

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full workflow.

## License

MIT
