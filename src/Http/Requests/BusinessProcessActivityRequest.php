<?php

namespace Crazybee47\Laravel\Bitrix24\Http\Requests;

class BusinessProcessActivityRequest extends BusinessProcessRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'event_token' => ['required', 'string'],
            ...parent::rules(),
        ];
    }

    public function getEventToken(): string
    {
        return $this->post('event_token');
    }

    public function getProperties(): ?array
    {
        return $this->post('properties');
    }

    public function getPropertyValue(string $key): ?string
    {
        return \Arr::get($this->getProperties(), $key);
    }
}
