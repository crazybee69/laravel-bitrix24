<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Http\Controllers;

use Crazybee47\Laravel\Bitrix24\Events\OnAppInstalled;
use Illuminate\Http\Request;

class ApplicationController
{
    public function onAppInstall(Request $request)
    {
        event(new OnAppInstalled());
    }
}
