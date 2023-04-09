<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Http\Controllers\Oauth;

use Crazybee47\Laravel\Bitrix24\BitrixService;
use Illuminate\Http\Request;

class AuthController
{
    public function __construct(private readonly BitrixService $bitrixService)
    {
    }

    public function callback(Request $request)
    {
        $this->bitrixService->authorizeRequest($request->toArray());

        return response('Авторизация прошла успешно!');
    }

    public function redirect(Request $request)
    {
        $url = __(':host/oauth/authorize/?client_id=:client_id&state=:state&redirect_uri=:redirect_uri', [
            'host' => config('services.bitrix.host'),
            'client_id' => config('services.bitrix.client_id'),
            'redirect_uri' => route('bitrix.oauth.callback'),
            'state' => ''
        ]);
        return redirect($url);
    }
}
