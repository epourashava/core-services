# Socialite for Core

```bash
composer require epourashava/core-services
```

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

## Socialite Usage

Please see the [Base Installation Guide](https://socialiteproviders.com/usage/), then follow the provider specific instructions below.

### Add configuration to `config/services.php`

```php
'ep-core' => [
  'client_id' => env('EP_CORE_CLIENT_ID'),
  'client_secret' => env('EP_CORE_CLIENT_SECRET'),
  'redirect' => env('EP_CORE_REDIRECT_URI'),
  'base_url' => env('EP_CORE_BASE_URL'),
],
```

### Add base URL to `.env`

Auth0 may require you to autorize against a custom URL, which you may provide as the base URL.

```bash
EP_CORE_BASE_URL=http://core.test/
EP_CORE_CLIENT_ID=your-client-id
EP_CORE_CLIENT_SECRET=your-client-secret
EP_CORE_REDIRECT_URI=http://your-callback-url
```

### Usage

You should now be able to use the provider like you would regularly use Socialite (assuming you have the facade installed):

```php
return Socialite::driver('ep-core')->redirect();
```

### Returned User fields

-   `id`
-   `nickname`
-   `name`
-   `email`
