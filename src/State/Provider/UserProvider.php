<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserProvider extends AbstractOfferProvider
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $developers = $this->collectionProvider->provide($operation, $uriVariables, $context);

        if (isset($context['filters']) && isset($context['filters']['order']) && $context['filters']['order'] === 'for-you') {
            try {
                $user = $this->getRelatedUser();
            } catch (\Exception $exception) {
                throw new UnauthorizedHttpException('Unauthorized', 'You must log in to use this feature');
            }
            $this->cacheMaker->makeCacheRequest('for_you_users_' . $user->getId(), self::CACHE_LIMIT);
            if ($this->cacheMaker->checkIfCacheExists()) {
                $developers = $this->deserialize($this->cacheMaker->getFromCache());
            } else {
                $developers = $this->orderService->calculateDevelopersForYouDisplayOrder($developers, $user);


                $this->cacheMaker->saveToCache($this->serialize($developers, $context));
            }
        }

        return $this->makePagination($developers, $operation, $context);
    }

}
