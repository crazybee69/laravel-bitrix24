<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Feature;

trait Event
{
    public function bindEvent(string $eventCode, string $handlerUrl): void
    {
        $this->api()->getMainScope()->event()->bind($eventCode, $handlerUrl);
    }

    public function unbindEvent(string $eventCode, string $handlerUrl): void
    {
        $this->api()->getMainScope()->event()->unbind($eventCode, $handlerUrl);
    }
}
