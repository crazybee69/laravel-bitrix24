<?php

declare(strict_types=1);

namespace Crazybee47\Laravel\Bitrix24\Feature;

trait Deal
{
    public function getDeal(int $id): array
    {
        return $this->getCoreApiClient()
            ->call('crm.deal.get', ['ID' => $id])
            ->getResponseData()
            ->getResult();
    }

    public function updateDeal(int $id, array $fields): void
    {
        $this->api()->getCRMScope()->deal()->update($id, $fields);
    }

    public function getDeals(array $filters = []): array
    {
        return $this->loadRecords('crm.deal.list', $filters, ['*', 'UF_*']);
    }
}
