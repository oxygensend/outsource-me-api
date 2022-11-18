<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JobOfferProvider extends AbstractOfferProvider
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $jobOffers = $this->collectionProvider->provide($operation, $uriVariables, $context);

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
                $jobOffers = $this->orderService->calculateJobOfferForYouDisplayOrder($jobOffers, $user);
                $this->cacheMaker->saveToCache($this->serialize($jobOffers, $context));
            }

        } else {

            $query = $this->requestStack->getCurrentRequest()->getQueryString();
            $query = str_replace('=', '-', $query); //forbidden character for redis
            $this->cacheMaker->makeCacheRequest('job_offers_' . $query, strpos($query, 'order-newest') ? 3600 : 86400);
            if ($this->cacheMaker->checkIfCacheExists()) {
                $jobOffers = $this->deserialize($this->cacheMaker->getFromCache(), $context);
            } else {
                $this->cacheMaker->saveToCache($this->serialize($jobOffers, $context));
            }
        }
        return $this->makePagination($jobOffers, $operation, $context);
    }

}
