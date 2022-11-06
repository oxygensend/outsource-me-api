<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;

class UserProvider extends  AbstractOfferProvider
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $developers = $this->collectionProvider->provide($operation, $uriVariables, $context);

        if (isset($context['filters']) && isset($context['filters']['order']) && $context['filters']['order'] === 'for-you') {
            $user = $this->getRelatedUser();
            $developers = $this->orderService->calculateDevelopersForYouDisplayOrder($developers, $user);
        }

        return $this->makePagination($developers, $operation);
    }

}
