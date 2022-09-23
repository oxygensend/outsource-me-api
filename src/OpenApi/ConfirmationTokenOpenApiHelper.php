<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;

final class ConfirmationTokenOpenApiHelper implements OpenApiFactoryInterface
{
    public function __construct(private readonly OpenApiFactoryInterface $decoraded)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decoraded)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $pathItem = new PathItem(
            ref: 'Confirmation Token',
            get: new Operation(
                operationId: 'confirmation_token',
                tags: ['Confirmation Token'],
                responses: [
                    '302' => [
                        'description' => 'Token accepted. Redirecting to configured URL.'
                    ],
                    '200' => null,
                    '400' => null,
                    '422' => null
                ],
                summary: 'Verifies confirmation token for user.',
                description: 'Verifies confirmation token sent to user by external message. Used for email verification and password reset confirmation',
                parameters: [
                [
                    'name' => 'type',
                    'in' => 'path',
                    'description' => 'Type of token',
                    'required' => true
                ],
                [
                    'name' => 'token',
                    'in' => 'path',
                    'description' => 'Confirmation token',
                    'required' => true
                ]
            ]
            )
        );

        $openApi->getPaths()->addPath('/confirmation_token/{type}/{token}', $pathItem);

        return $openApi;
    }

}