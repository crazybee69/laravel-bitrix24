<?php

namespace Crazybee47\Laravel\Bitrix24;

enum EventTypeEnum: string
{
    case OnAppInstall = 'ONAPPINSTALL';
    case OnAppUninstall = 'ONAPPUNINSTALL';
}
