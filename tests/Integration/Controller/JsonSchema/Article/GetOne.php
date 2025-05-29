<?php

namespace App\Tests\Integration\Controller\JsonSchema\Article;

use App\Tests\Fixtures\Controller\Article\Get;
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
                    'required'              => ['id', 'title', 'content', 'author_id', 'created_at', 'updated_at'],
                    'additionalProperties'  => false,
                    'properties'    => [
                        'id'            => ['type' => 'integer'],
                        'title'         => ['enum' => [Get::ARTICLE1_TITLE]],
                        'content'       => ['enum' => [Get::ARTICLE1_CONTENT]],
                        'author_id'     => ['type' => 'integer'],
                        'created_at'    => ['type' => 'string'],
                        'updated_at'    => ['type' => 'null'],
                    ]
                ]
            ]
        ];
    }
}