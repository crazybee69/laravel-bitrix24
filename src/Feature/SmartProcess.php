<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Feature;

trait SmartProcess
{
    public function getSmartProcessList(int $smartProcessId, array $filters = []): array
    {
        return $this->loadRecords('crm.item.list', $filters, [], ['entityTypeId' => $smartProcessId], 'items');
    }

    public function updateSmartProcess(int $smartProcessId, int $id, array $fields): void
    {
        $this->getApiClient()->call('crm.item.update', [
            'id' => $id,
            'entityTypeId' => $smartProcessId,
            'fields' => $fields,
        ]);
    }
}
