<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model as Model;

final class OAuth2GoogleConnectionOpenApiHelper implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    )
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $pathItem = new Model\PathItem(
            ref: 'User',
            get: new Model\Operation(
                operationId: 'Google OAuth authentication',
                tags: ['User'],
                responses: [
                    '302' => [
                        'description' => 'Authenticating user with Google OAuth protocol and redirecting to configured URL',
                    ],
                ],
                summary: 'Authenticate user with Google OAuth protocol',

            ),
        );
        $openApi->getPaths()->addPath('/connect/google', $pathItem);

        return $openApi;
    }
}