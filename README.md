# Introduction

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

## Setup Bitrix24 Rest API Application

- Путь вашего обработчика: `{APP_URL}/bitrix/oauth/callback`
- Путь для первоначальной установки: `{APP_URL}/bitrix/install`
  
By default, authorization data is saved at the time of application installation. It is also possible to manually install the application for a specific Bitrix24 user. To do this, you need to direct the user to the link: `{APP_URL}/bitrix/oauth/redirect`

## Configuration

These credentials should be placed in your application's `config/services.php` configuration file:
```php
'bitrix' => [
    'host' => 'https://example.bitrix24.ru',
    'client_id' => env('BITRIX_CLIENT_ID'),
    'client_secret' => env('BITRIX_CLIENT_SECRET'),
    'scopes' => explode(',', env('BITRIX_SCOPES', 'crm')),
]
```

## Setup App actions for Bitrix24

If you need to install webhooks or application actions for Business Processes when the application is installed into Bitrix24, you can listen the event: `Crazybee47\Laravel\Bitrix24\Events\OnAppInstalled`. To do this, register handler for it in your EventServiceProvider:

### Example:

```php
protected $listen = [
    \Crazybee47\Laravel\Bitrix24\Events\OnAppInstalled::class => [
        'App\Listeners\RegisterBitrixWebhooks@handle'
    ],
];
```
