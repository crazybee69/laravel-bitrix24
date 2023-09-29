# Introduction

Laravel wrapper around OAuth2 Bitrix24 Rest API for a quick start of a new application on Laravel + Bitrix24.

There are few methods implemented in the library for interacting with the API, they will be added gradually.

If you need more methods, you can extend BitrixService and make API requests using the `getApiClient` method, or `loadRecords` method for fetching all items from lists.

### Example:
```php
class ExtendedBitrixService extends \Crazybee47\Laravel\Bitrix24\BitrixService {

    public function getContact(int $id): array {
        return $this->getApiClient()
            ->call('crm.contact.get', ['ID' => $id])
            ->getResponseData()
            ->getResult();
    }
    
    public function getContactList(array $filters = []): array {
        return $this->loadRecords('crm.contact.list', $filters);
    }
}
```

# Installation

## Configuration

These credentials should be placed in your application's `config/services.php` configuration file:
```php
'bitrix' => [
    'host' => 'https://example.bitrix24.ru',
    'client_id' => env('BITRIX_CLIENT_ID'),
    'client_secret' => env('BITRIX_CLIENT_SECRET'),
    'scopes' => explode(',', env('BITRIX_SCOPES', 'crm')),//crm,bizproc,telephony
]
```

Add the `bitrix/*` pattern to the list of VerifyCsrfToken middleware except list.
```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'bitrix/*',
    ];
}
```

## Setup Bitrix24 Rest API Application

- Handler path: `{APP_URL}/bitrix/oauth/callback`
- Install handler path: `{APP_URL}/bitrix/install`
  
By default, authorization data is saved at the time of application installation. It is also possible to manually authorize specific Bitrix24 user into the application. To do this, you need to direct the user to the link: `{APP_URL}/bitrix/oauth/redirect`

## Setup App actions for Bitrix24

If you need to install webhooks or application actions for Business Processes when the application is installed into Bitrix24, you can listen the event: `Crazybee47\Laravel\Bitrix24\Events\OnAppInstalled`. To do this, register handler for it in your EventServiceProvider:

### Example:

```php
protected $listen = [
    \Crazybee47\Laravel\Bitrix24\Events\OnAppInstall::class => [
        'App\Listeners\RegisterBitrixWebhooks@handle'
    ],
];
```
