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

            $this->cacheMaker->makeCacheRequest('for_you_offers_' . $user->getId(), self::CACHE_LIMIT);
            if ($this->cacheMaker->checkIfCacheExists()) {
                $jobOffers = $this->cacheMaker->getFromCache();
            } else {
                $jobOffers = $this->orderService->calculateJobOfferForYouDisplayOrder($jobOffers, $user);
                $this->cacheMaker->saveToCache($jobOffers);
            }
        }

        return $this->makePagination($jobOffers, $operation, $context);
    }

}
