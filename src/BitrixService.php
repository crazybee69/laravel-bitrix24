<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24;

use Bitrix24\SDK\Core\Batch;
use Bitrix24\SDK\Core\BulkItemsReader\BulkItemsReaderBuilder;
use Bitrix24\SDK\Core\Contracts\CoreInterface;
use Bitrix24\SDK\Core\CoreBuilder;
use Bitrix24\SDK\Events\AuthTokenRenewedEvent;
use Bitrix24\SDK\Services\ServiceBuilder;
use Crazybee47\Laravel\Bitrix24\Feature\BusinessProcess;
use Crazybee47\Laravel\Bitrix24\Feature\Deal;
use Crazybee47\Laravel\Bitrix24\Feature\OauthAuthorizable;
use Crazybee47\Laravel\Bitrix24\Feature\SmartProcess;
use Crazybee47\Laravel\Bitrix24\Feature\Webhook;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BitrixService
{
    use BusinessProcess;
    use Deal;
    use OauthAuthorizable;
    use SmartProcess;
    use Webhook;

    private const OAUTH_DATA_CACHE_KEY = 'bitrix:oauth-data';
    private const TIMEOUT_BETWEEN_REQUESTS = 500000;

    private readonly ?ServiceBuilder $serviceBuilder;
    private readonly ?EventDispatcher $eventDispatcher;

    public function __construct(
        private readonly array $config,
    ) {
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addListener(
            AuthTokenRenewedEvent::class,
            fn(AuthTokenRenewedEvent $event) => $this->onAccessTokenRenewed($event)
        );
    }

    /**
     * @param string $method
     * @param array $filter
     * @return array
     */
    protected function loadRecords(
        $method,
        array $filters,
        array $fields = [],
        array $additionalParams = [],
        ?string $resultKey = null
    ) {
        $params = [
            'filter' => $filters,
            'start' => 0,
        ];
        if (!empty($fields)) {
            if (array_key_exists('select', $params)) {
                $params['select'] = array_merge($params['select'], $fields);
            } else {
                $params['select'] = $fields;
            }
        }
        if (!empty($additionalParams)) {
            $params = array_merge($params, $additionalParams);
        }
        while (true) {
            $response = $this->getApiClient()->call($method, $params)->getHttpResponse();
            $response = json_decode($response->getContent(), true);
            if ($resultKey === null) {
                $results[] = $response['result'];
            } else {
                $results[] = $response['result'][$resultKey];
            }
            if (isset($response['next'])) {
                $params['start'] = $response['next'];
                usleep(self::TIMEOUT_BETWEEN_REQUESTS);
            } else {
                break;
            }
        }
        return array_merge([], ...$results);
    }

    protected function getApiClient(): CoreInterface
    {
        return $this->api()->getMainScope()->main()->core;
    }

    private function api(): ServiceBuilder
    {
        if ($this->serviceBuilder === null) {
            $appProfile = new \Bitrix24\SDK\Core\Credentials\ApplicationProfile(
                $this->config['client_id'],
                $this->config['client_secret'],
                new \Bitrix24\SDK\Core\Credentials\Scope($this->config['scopes'] ?? [])
            );
            $token = $this->getCachedToken();
            $domain = $this->config['host'];
            $credentials = \Bitrix24\SDK\Core\Credentials\Credentials::createFromOAuth($token, $appProfile, $domain);

            $logger = new NullLogger();

            $core = (new CoreBuilder())
                ->withCredentials($credentials)
                ->withLogger($logger)
                ->withEventDispatcher($this->eventDispatcher)
                ->build();

            $batch = new Batch($core, $logger);
            $bulkItemsReader = (new BulkItemsReaderBuilder($core, $batch, $logger))->build();
            $this->serviceBuilder = new ServiceBuilder(
                $core,
                $batch,
                $bulkItemsReader,
                $logger
            );
        }
        return $this->serviceBuilder;
    }
}
