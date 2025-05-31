<?php

namespace App\Tests\Integration\Controller\JsonSchema\User;

use App\Tests\Fixtures\Controller\User\Get;
use Symfony\Component\HttpFoundation\Response;

class GetOne
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
                    'enum' => ['']
                ],
                'data'      => [
                    'type'                  => 'object',
                    'required'              => ['id', 'email', 'name', 'role'],
                    'additionalProperties'  => false,
                    'properties'    => [
                        'id'    => ['type' => 'integer'],
                        'email' => ['enum' => [Get::USER_AUTHOR_EMAIL]],
                        'name'  => ['enum' => [Get::USER_AUTHOR_NAME]],
                        'role'  => ['enum' => [Get::USER_AUTHOR_ROLE->value]]
                    ]
                ]
            ]
        ];
    }
}