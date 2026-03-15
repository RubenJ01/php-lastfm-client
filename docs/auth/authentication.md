# Authentication

Some Last.fm API methods (e.g. scrobbling) require authentication. This library implements the [Last.fm desktop authentication flow](https://www.last.fm/api/desktopauth).

**Last.fm API Reference:** [auth.getToken](https://lastfm-docs.github.io/api-docs/auth/getToken/), [auth.getSession](https://lastfm-docs.github.io/api-docs/auth/getSession/)

## Prerequisites

You need an **API key** and **API secret** (shared secret) from the [Last.fm API account page](https://www.last.fm/api/account/create).

## Authentication Flow

### Step 1: Get a Token

```php
use Rjds\PhpLastfmClient\LastfmClient;

$client = new LastfmClient(
    apiKey: 'your-api-key',
    apiSecret: 'your-api-secret',
);

$token = $client->auth()->getToken();
```

### Step 2: Authorize the Token

Direct the user to the authorization URL. They must grant access in their browser:

```php
$authUrl = $client->auth()->getAuthUrl($token);

echo "Please visit: {$authUrl}\n";
echo "Press Enter after authorizing...\n";
```

For web apps, you can pass a callback URL:

```php
$authUrl = $client->auth()->getAuthUrl($token, 'https://yourapp.com/callback');
// User will be redirected back to your callback URL after authorizing
```

### Step 3: Get a Session Key

After the user has authorized, exchange the token for a session key:

```php
$session = $client->auth()->getSession($token);

echo "Authenticated as: {$session->name}\n";
echo "Session key: {$session->key}\n";
```

**Save the session key** — it doesn't expire and can be reused for future requests.

### Step 4: Use the Session Key

Set the session key on the existing client, or create a new one:

```php
use Rjds\PhpLastfmClient\Dto\Track\Scrobble;

// Option A: set session key on the same client
$client->setSessionKey($session->key);

// Option B: create a new client with all credentials
$client = new LastfmClient('your-api-key', 'your-api-secret', $session->key);

// Now you can scrobble, love tracks, etc.
$client->track()->scrobble(new Scrobble(
    artist: 'Radiohead',
    track: 'Karma Police',
    timestamp: time(),
));
```

## SessionDto Properties

| Property     | Type     | Description                          |
|--------------|----------|--------------------------------------|
| `name`       | `string` | The authenticated user's username.   |
| `key`        | `string` | The session key (save this!).        |
| `subscriber` | `bool`   | Whether the user is a subscriber.    |

## Complete Example

```php
use Rjds\PhpLastfmClient\LastfmClient;

// 1. Create client with API credentials
$client = new LastfmClient(
    apiKey: 'your-api-key',
    apiSecret: 'your-api-secret',
);

// 2. Get token
$token = $client->auth()->getToken();

// 3. User authorizes in browser
$authUrl = $client->auth()->getAuthUrl($token);
echo "Visit: {$authUrl}\n";

// Wait for user to authorize...

// 4. Exchange token for session
$session = $client->auth()->getSession($token);
echo "Session key: {$session->key}\n";

// 5. Set the session key and make authenticated calls
$client->setSessionKey($session->key);
```
