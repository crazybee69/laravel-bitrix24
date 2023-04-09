<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Feature;

use Bitrix24\SDK\Core\Credentials\AccessToken;
use Bitrix24\SDK\Events\AuthTokenRenewedEvent;
use Crazybee47\Laravel\Bitrix24\Exception\UndefinedOauthDataException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

trait OauthAuthorizable
{
    public function authorizeRequest(array $request): void
    {
        $code = $request['code'] ?? null;
        $serverDomain = $request['server_domain'] ?? 'oauth.bitrix.info';
        if ($code !== null) {
            $this->getAccessTokenByCode($code, $serverDomain);
        } else {
            $this->saveOauthData($request);
        }
    }

    private function getAccessTokenByCode(string $code, string $serverDomain): void
    {
        $httpClient = new Client();
        $response = $httpClient->get("https://{$serverDomain}/oauth/token/", [
            RequestOptions::QUERY => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'code' => $code
            ]
        ]);
        $oauthData = json_decode($response->getBody()->getContents(), true);
        $this->saveOauthData($oauthData);
    }

    public function saveOauthData(array $oauthData): void
    {
        Cache::put(self::OAUTH_DATA_CACHE_KEY, $oauthData, 86400);
    }

    private function onAccessTokenRenewed(AuthTokenRenewedEvent $event): void
    {
        $token = $event->getRenewedToken()->getAccessToken();
        $oauthData = [
            'access_token' => $token->getAccessToken(),
            'expires' => $token->getRefreshToken(),
            'refresh_token' => $token->getExpires()
        ];
        $cachedOauthData = Cache::get(self::OAUTH_DATA_CACHE_KEY);
        $oauthData = array_merge($cachedOauthData, $oauthData);
        $this->saveOauthData($oauthData);
    }

    /**
     * @return AccessToken
     * @throws UndefinedOauthDataException
     */
    private function getCachedToken(): AccessToken
    {
        $oauthData = Cache::get(self::OAUTH_DATA_CACHE_KEY);
        if ($oauthData === null) {
            throw new UndefinedOauthDataException();
        }

        return new AccessToken(
            Arr::get($oauthData, 'access_token'),
            Arr::get($oauthData, 'refresh_token'),
            Arr::get($oauthData, 'expires'),
        );
    }
}
