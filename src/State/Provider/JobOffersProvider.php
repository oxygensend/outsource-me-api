<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JobOffersProvider extends AbstractOfferProvider
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $context['filters']['archived'] = "0";


        if (isset($context['filters']) && isset($context['filters']['order']) && $context['filters']['order'] === 'for-you') {
            try {
                $user = $this->getRelatedUser();
            } catch (\Exception $exception) {
                throw new UnauthorizedHttpException('Unauthorized', 'You must log in to use this feature');
            }

            $this->cacheMaker->makeCacheRequest('for_you_offers_' . $user->getId(), self::CACHE_LIMIT);
            if ($this->cacheMaker->checkIfCacheExists()) {
                $jobOffers = $this->deserialize($this->cacheMaker->getFromCache());
            } else {
                $jobOffers = $this->collectionProvider->provide($operation, $uriVariables, $context);
                $jobOffers = $this->orderService->calculateJobOfferForYouDisplayOrder($jobOffers, $user);
                $this->cacheMaker->saveToCache($this->serialize($jobOffers, $context));
            }

        } else {
            $jobOffers = $this->collectionProvider->provide($operation, $uriVariables, $context);
        }


        return $this->makePagination($jobOffers, $operation, $context);
    }

}
