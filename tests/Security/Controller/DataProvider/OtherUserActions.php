<?php

namespace App\Tests\Security\Controller\DataProvider;

use App\Security\UserRole;

class OtherUserActions
{
    private const URIS = [
        [
            'method'    => 'GET',
            'uri'       => '/api/users',
            'name'      => 'get all users'
        ],
        [
            'method'    => 'GET',
            'uri'       => '/api/users/10',
            'name'      => 'get a user'
        ],
        [
            'method'    => 'POST',
            'uri'       => '/api/users',
            'name'      => 'create a user'
        ],
        [
            'method'    => 'PUT',
            'uri'       => '/api/users/10',
            'name'      => 'update a user'
        ],
        [
            'method'    => 'DELETE',
            'uri'       => '/api/users/10',
            'name'      => 'delete a user'
        ]
    ];

    private const ROLES = [
        'UNAUTHENTICATED'   => [
            'role'          => null,
            'isForbidden'   => true
        ],
        'ADMIN'             => [
            'role'          => UserRole::ADMIN,
            'isForbidden'   => false
        ],
        'AUTHOR'            => [
            'role'          => UserRole::AUTHOR,
            'isForbidden'   => true
        ],
        'READER'            => [
            'role'          => UserRole::READER,
            'isForbidden'   => true
        ]
    ];

    /**
     * @return array<string, array{
     *     role: ?UserRole,
     *     isForbidden: bool
     * }>
     */
    public static function get(): array
    {
        $data = [];

        foreach (self::URIS as $uri) {
            foreach (self::ROLES as $role) {
                $datasetName =
                    ($role['role'] ? $role['role']->value : 'UNAUTHENTICATED') .
                    ' should' . ($role['isForbidden'] ? ' NOT' : '') . ' be able to ' .
                    $uri['name'];

                $data[$datasetName] = [
                    'requestMethod' => $uri['method'],
                    'requestUri'    => $uri['uri'],
                    'role'          => $role['role'],
                    'isForbidden'   => $role['isForbidden']
                ];
            }
        }

        return $data;
    }
}