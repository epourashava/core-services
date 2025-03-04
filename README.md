# Core Services

This package provides a set of services for Laravel applications. It includes:

-   Socialite Provider for Core Auth
-   Core API Client
-   Core User Model (for use with the Core API)

## Installation & Basic Usage

Add this into your composer.json file

```
"repositories": [
    {
        "url": "https://github.com/epourashava/core-services.git",
        "type": "git"
    }
],
```

```bash
composer require epourashava/core-services
```

## Socialite Usage

We use Socialite for authentication. To use the Core provider, you need to add the following to your `config/services.php` file:

```php
'ep-core' => [
  'client_id' => env('EP_CORE_CLIENT_ID'),
  'client_secret' => env('EP_CORE_CLIENT_SECRET'),
  'redirect' => env('EP_CORE_REDIRECT_URI'),
  'base_url' => env('EP_CORE_BASE_URL'),
],
```

### Add variables to `.env`

Core Auth may require you to authorize against a custom URL, which you may provide as the base URL.

```bash
EP_CORE_BASE_URL=http://core.test/
EP_CORE_CLIENT_ID=your-client-id
EP_CORE_CLIENT_SECRET=your-client-secret
EP_CORE_REDIRECT_URI=http://your-callback-url
```

### Usage

You should now be able to use the provider like you would regularly use Socialite (assuming you have the facade installed or run `composer require laravel/socialite`):

```php
return Socialite::driver('ep-core')->redirect();
```

### Callback

```php
$user = Socialite::driver('ep-core')->user();

$token = $user->token;
$refreshToken = $user->refreshToken;
$expiresIn = $user->expiresIn;

// $user->getId();
// $user->getNickname();
// $user->getName();
// $user->getEmail();
// $user->getAvatar();
```

## Model

The Core User model is a simple model that extends Laravel's default User model. It includes a few extra fields that are returned from the Core API.

### Usage

```php

use Core\Models\User as CoreUser;

class User extends CoreUser
{
    //
}
```

Update the migration file to use the Core User model:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->text('access_token')->nullable();
    $table->text('refresh_token')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->string('avatar')->nullable();
    $table->string('provider')->default('core');
    $table->rememberToken();
    $table->timestamps();
});
```

## API Client

The Core API Client is a simple client that allows you to make requests to the Core API. It uses Laravel's HTTP client.

### Usage

```php
use Core\Client;

$client = new Client();

$response = $client->get('/users');

// Or use the OAuth2Client class to make authenticated requests
use Core\OAuth2Client;

$client = OAuth2Client::instance();

// Get the user
$response = $client->getUser();
```
