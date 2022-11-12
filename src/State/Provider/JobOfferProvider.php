<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;

class JobOfferProvider extends AbstractOfferProvider
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $jobOffers = $this->collectionProvider->provide($operation, $uriVariables, $context);

        if (isset($context['filters']) && isset($context['filters']['order']) && $context['filters']['order'] === 'for-you') {
            $user = $this->getRelatedUser();
            $jobOffers = $this->orderService->calculateJobOfferForYouDisplayOrder($jobOffers, $user);
        }

        return $this->makePagination($jobOffers, $operation, $context);
    }
}
