<?php

namespace App\Tests\Security\Controller\DataProvider;

use App\Security\UserRole;

class Article
{
    /**
     * @return array<string, array{
     *     role: ?UserRole,
     *     isForbidden: bool
     * }>
     */
    public static function provideForGet(): array
    {
        return [
            'UNAUTHENTICATED should NOT be able to get article(s)'    => [
                'role'          => null,
                'isForbidden'   => true
            ],
            'ADMIN should be able to get article(s)'                  => [
                'role'          => UserRole::ADMIN,
                'isForbidden'   => false
            ],
            'AUTHOR should be able to get article(s)'                 => [
                'role'          => UserRole::AUTHOR,
                'isForbidden'   => false
            ],
            'READER should be able to get article(s)'                 => [
                'role'          => UserRole::READER,
                'isForbidden'   => false
            ]
        ];
    }

    /**
     * @return array<string, array{
     *     role: ?UserRole,
     *     isForbidden: bool
     * }>
     */
    public static function provideForCreate(): array
    {
        return [
            'UNAUTHENTICATED should NOT be able to create an article'   => [
                'role'          => null,
                'isForbidden'   => true
            ],
            'ADMIN should be able to create an article'                 => [
                'role'          => UserRole::ADMIN,
                'isForbidden'   => false
            ],
            'AUTHOR should be able to create an article'                => [
                'role'          => UserRole::AUTHOR,
                'isForbidden'   => false
            ],
            'READER should NOT be able to create an article'            => [
                'role'          => UserRole::READER,
                'isForbidden'   => true
            ]
        ];
    }

    /**
     * @return array<string, array{
     *     userEmail: ?string,
     *     articleIndex: int,
     *     isForbidden: bool
     * }>
     */
    public static function provideForUpdate(): array
    {
        return [
            'UNAUTHENTICATED should NOT be able to update an article'   => [
                'userEmail'     => null,
                'articleIndex'  => 1,
                'isForbidden'   => true
            ],
            'ADMIN should be able to update the article #1'             => [
                'userEmail'     => 'admin@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => false
            ],
            'ADMIN should be able to update the article #2'             => [
                'userEmail'     => 'admin@bar.com',
                'articleIndex'  => 2,
                'isForbidden'   => false
            ],
            'AUTHOR1 should be able to update the article #1'           => [
                'userEmail'     => 'author1@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => false
            ],
            'AUTHOR2 should be able to update the article #2'           => [
                'userEmail'     => 'author2@bar.com',
                'articleIndex'  => 2,
                'isForbidden'   => false
            ],
            'AUTHOR1 should NOT be able to update the article #2'       => [
                'userEmail'     => 'author1@bar.com',
                'articleIndex'  => 2,
                'isForbidden'   => true
            ],
            'AUTHOR2 should NOT be able to update the article #1'       => [
                'userEmail'     => 'author2@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => true
            ],
            'READER should NOT be able to update an article'            => [
                'userEmail'     => 'reader@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => true
            ],
        ];
    }

    /**
     * @return array<string, array{
     *     userEmail: ?string,
     *     articleIndex: int,
     *     isForbidden: bool
     * }>
     */
    public static function provideForDelete(): array
    {
        return [
            'UNAUTHENTICATED should NOT be able to delete an article'   => [
                'userEmail'     => null,
                'articleIndex'  => 1,
                'isForbidden'   => true
            ],
            'ADMIN should be able to delete the article #1'             => [
                'userEmail'     => 'admin@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => false
            ],
            'ADMIN should be able to delete the article #2'             => [
                'userEmail'     => 'admin@bar.com',
                'articleIndex'  => 2,
                'isForbidden'   => false
            ],
            'AUTHOR1 should be able to delete the article #1'           => [
                'userEmail'     => 'author1@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => false
            ],
            'AUTHOR2 should be able to delete the article #2'           => [
                'userEmail'     => 'author2@bar.com',
                'articleIndex'  => 2,
                'isForbidden'   => false
            ],
            'AUTHOR1 should NOT be able to delete the article #2'       => [
                'userEmail'     => 'author1@bar.com',
                'articleIndex'  => 2,
                'isForbidden'   => true
            ],
            'AUTHOR2 should NOT be able to delete the article #1'       => [
                'userEmail'     => 'author2@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => true
            ],
            'READER should NOT be able to delete an article'            => [
                'userEmail'     => 'reader@bar.com',
                'articleIndex'  => 1,
                'isForbidden'   => true
            ]
        ];
    }
}