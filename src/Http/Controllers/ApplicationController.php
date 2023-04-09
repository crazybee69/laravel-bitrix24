<?php

namespace Crazybee47\Laravel\Bitrix24\Http\Controllers;

use Crazybee47\Laravel\Bitrix24\Events\OnAppInstalled;
use Illuminate\Http\Request;

class ApplicationController
{
    public function onAppInstall(Request $request)
    {
        event(new OnAppInstalled());
    }

    public function handle(Request $request)
    {
        //@todo handling app webhooks
    }
}
