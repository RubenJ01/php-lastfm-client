# Php Lastfm Client

A client that integrates the LastFM API

## Requirements

- PHP >= 8.4

## Installation

```bash
composer require rjds/php-lastfm-client
```

## Usage

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient('your-api-key');

// Get user info
$user = $client->user()->getInfo('rj');

echo $user->name;        // "RJ"
echo $user->playcount;   // 150316
echo $user->country;     // "United Kingdom"
echo $user->subscriber;  // true
echo $user->registered->format('Y-m-d'); // "2002-11-20"
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
