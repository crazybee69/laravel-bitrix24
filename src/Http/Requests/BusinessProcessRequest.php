<?php

namespace App\Http\Requests\Bitrix\Webhook;

use Illuminate\Foundation\Http\FormRequest;

class BusinessProcessRequest extends FormRequest
{
    private string $entity;

    private int $entityId;

    private ?int $entityTypeId;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'document_id' => ['required', 'array']
        ];
    }

    protected function passedValidation()
    {
        $this->parseBusinessProcessDocumentId();
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getEntityTypeId(): ?int
    {
        return $this->entityTypeId;
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function parseBusinessProcessDocumentId(): void
    {
        $bpParamMarker = $this->get('document_id');
        $documentId = \Arr::get($bpParamMarker, 2);
        $documentIdParts = explode('_', $documentId);
        if (count($documentIdParts) === 2) {
            $this->entity = $documentIdParts[0];
            $this->entityId = $documentIdParts[1];
        } elseif (count($documentIdParts) === 3) {
            $this->entity = $documentIdParts[0];
            $this->entityTypeId = $documentIdParts[1];
            $this->entityId = $documentIdParts[2];
        } else {
            throw new \Exception('Unknown webhook format');
        }
    }
}
