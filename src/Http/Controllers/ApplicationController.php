<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Http\Controllers;

use Crazybee47\Laravel\Bitrix24\BitrixService;
use Crazybee47\Laravel\Bitrix24\Events\OnAppInstalled;
use Illuminate\Http\Request;

class ApplicationController
{
    public function __construct(
        private readonly BitrixService $bitrixService
    ) {
    }

    public function onAppInstall(Request $request)
    {
        $this->bitrixService->authorizeRequest($request->toArray());
        event(new OnAppInstalled());
    }
}
