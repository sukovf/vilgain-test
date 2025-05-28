<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use App\Security\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, mixed>
 */
class ArticleVoter extends Voter
{
    public const CREATE = 'CREATE';
    public const UPDATE = 'UPDATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::UPDATE])) {
            return false;
        }

        if (!($subject instanceof Article)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            return false;
        }

        if (!($subject instanceof Article)) {
            return false;
        }

        return match($attribute) {
            self::CREATE    => $this->canCreate($user),
            self::UPDATE    => $this->canUpdate($user, $subject),
            default         => false
        };
    }

    private function canCreate(User $user): bool
    {
        return
            $user->getRole() === UserRole::ADMIN ||
            $user->getRole() === UserRole::AUTHOR;
    }

    private function canUpdate(User $user, Article $article): bool
    {
        return
            $user->getRole() === UserRole::ADMIN ||
            $article->getAuthor() === $user;
    }
}