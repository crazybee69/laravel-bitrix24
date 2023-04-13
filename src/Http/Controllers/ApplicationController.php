<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Http\Controllers;

use Crazybee47\Laravel\Bitrix24\BitrixService;
use Crazybee47\Laravel\Bitrix24\Events\OnAppInstall;
use Crazybee47\Laravel\Bitrix24\Events\OnAppUninstall;
use Crazybee47\Laravel\Bitrix24\EventTypeEnum;
use Illuminate\Http\Request;

class ApplicationController
{
    public function __construct(
        private readonly BitrixService $bitrixService
    ) {
    }

    public function onAppInstall(Request $request)
    {
        event(new OnAppInstall());
        $this->bitrixService->authorizeRequest($request->toArray());
        $this->bitrixService->bindEvent(EventTypeEnum::OnAppUninstall->value, route('bitrix.uninstall'));
    }

    public function onAppUnInstall(Request $request)
    {
        event(new OnAppUninstall());
    }
}
