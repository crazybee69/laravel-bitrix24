<?php

namespace Crazybee47\Laravel\Bitrix24\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GlobalWebhookRequest extends FormRequest
{
    public function rules()
    {
        return [
            'event' => ['required', 'string'],
            'data' => ['required', 'array'],
        ];
    }

    public function getEvent(): string
    {
        return $this->post('event');
    }

    public function getData(): array
    {
        return $this->post('data');
    }

    public function getDataParamValue(string $key): ?string
    {
        return $this->getData()[$key] ?? null;
    }
}
