<?php

namespace Crazybee47\Laravel\Bitrix24\Feature;

trait Webhook
{
    public function bindWebhook(string $eventCode, string $handlerUrl): void
    {
        $this->api()->getMainScope()->event()->bind($eventCode, $handlerUrl);
    }
}
