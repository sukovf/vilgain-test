<?php

namespace App\Tests\Integration\Controller\JsonSchema\User;

use App\Tests\Fixtures\Controller\User\Get;
use Symfony\Component\HttpFoundation\Response;

class GetAll
{
    /**
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        return [
            'type'                  => 'object',
            'required'              => ['code', 'message', 'data'],
            'additionalProperties'  => false,
            'properties'            => [
                'code'      => [
                    'enum' => [Response::HTTP_OK]
                ],
                'message'   => [
                    'type' => 'string'
                ],
                'data'      => [
                    'type'      => 'array',
                    'minItems'  => 2,
                    'maxItems'  => 2,
                    'items'     => [
                        [
                            'type'                  => 'object',
                            'required'              => ['id', 'email', 'name', 'role'],
                            'additionalProperties'  => false,
                            'properties'    => [
                                'id'    => ['type' => 'integer'],
                                'email' => ['enum' => [Get::USER_AUTHOR_EMAIL]],
                                'name'  => ['enum' => [Get::USER_AUTHOR_NAME]],
                                'role'  => ['enum' => [Get::USER_AUTHOR_ROLE->value]]
                            ]
                        ],
                        [
                            'type'                  => 'object',
                            'required'              => ['id', 'email', 'name', 'role'],
                            'additionalProperties'  => false,
                            'properties'            => [
                                'id'    => ['type' => 'integer'],
                                'email' => ['enum' => [Get::USER_READER_EMAIL]],
                                'name'  => ['enum' => [Get::USER_READER_NAME]],
                                'role'  => ['enum' => [Get::USER_READER_ROLE->value]]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}