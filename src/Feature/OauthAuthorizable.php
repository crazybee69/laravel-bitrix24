<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Feature;

use Bitrix24\SDK\Core\Credentials\AccessToken;
use Bitrix24\SDK\Events\AuthTokenRenewedEvent;
use Crazybee47\Laravel\Bitrix24\EventTypeEnum;
use Crazybee47\Laravel\Bitrix24\Exception\UndefinedOauthDataException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait OauthAuthorizable
{
    public function authorizeRequest(array $request): void
    {
        $event = $request['event'] ?? null;
        if ($event === EventTypeEnum::OnAppInstall->value) {
            $isSimpleAuth = Arr::has($request, 'AUTH_ID');
            if ($isSimpleAuth) {
                $redirectUrl = route('bitrix.oauth.redirect');
                Log::warning("[BitrixService] Authorization error. Bitrix24 use simple auth. You must authorize by redirect url: {$redirectUrl}");
                return;
            }
            $authData = $request['auth'];
            $this->saveOauthData($authData);
            return;
        }

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

    protected function saveOauthData(array $oauthData): void
    {
        Cache::forever(self::OAUTH_DATA_CACHE_KEY, $oauthData);
    }

    protected function getCachedOauthData(): array
    {
        return Cache::get(self::OAUTH_DATA_CACHE_KEY);
    }

    private function onAccessTokenRenewed(AuthTokenRenewedEvent $event): void
    {
        $token = $event->getRenewedToken()->getAccessToken();
        $oauthData = [
            'access_token' => $token->getAccessToken(),
            'expires' => $token->getExpires(),
            'refresh_token' => $token->getRefreshToken()
        ];
        $cachedOauthData = $this->getCachedOauthData();
        $oauthData = array_merge($cachedOauthData, $oauthData);
        $this->saveOauthData($oauthData);
    }

    /**
     * @return AccessToken
     * @throws UndefinedOauthDataException
     */
    private function getCachedToken(): AccessToken
    {
        $oauthData = $this->getCachedOauthData();
        if ($oauthData === null) {
            throw new UndefinedOauthDataException();
        }

        return new AccessToken(
            Arr::get($oauthData, 'access_token'),
            Arr::get($oauthData, 'refresh_token'),
            (int)Arr::get($oauthData, 'expires'),
        );
    }
}
