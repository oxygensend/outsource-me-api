<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model as Model;

final class TokenCreateOpenApiHelper implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['CreateToken.Response'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'example' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NjM3ODI0MzQsImV4cCI6MTY2Mzc4NjAzNCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InRlc3RAdGVzdC5jb20ifQ.jD498ZajziSCo74_iw8F20Ck__eQwyhLduCH_hkgiFFjy-CMVOuwB0JJk7oawAbqSRrkUhFwSlbsqnSL3IY2mFKZMzz2uj8r2Q6bKGKGtWg9RMo31yn5DgC286YqgzBYdpmvHDxps1vCZxd4WHWb4UJ1-n7gp-5cVlSjFxQ49Utwja28bxMhQhZ_vvpj3paGSBw3rlYqFvdukBLTUlezJoSpjdW9wiNHgohwmvvCBoVHeVUZw_x5-gUwjU7CCiMvTXQbDTkksToUsOMrXWT8f7mVfFsJOi4qI8ALMUlA5yN-Hbzwg7vkeoSkeyXtVvzA2jVpvw4e_vIJ_M3xSuu40A',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'd776750a1111590634397a4bf2ee562949c9fabd9830b6e967d399a8e9b89f0747992b3d89027269e5e233073561b9782734a66cd65453001c733d1fa8cba6a9',
                    'readOnly' => true
                ]
            ],
        ]);
        $schemas['CreateToken.Request'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'johndoe@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                operationId: 'authentication',
                tags: ['User'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/CreateToken.Response',
                                ],
                            ],
                        ],
                    ],
                    '401' => new Model\Response('Invalid credentials'),
                    '400' => new Model\Response('Bad request')
                ],
                summary: 'Get JWT token to login.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/CreateToken.Request',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/api/login', $pathItem);

        return $openApi;
    }
}