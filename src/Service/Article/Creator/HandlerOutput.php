<?php

namespace App\Service\Article\Creator;

use App\Entity\Article;

class HandlerOutput
{
    public function __construct(
        private readonly Article $article,
        private readonly int     $authorUserId
    ) {}

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function getAuthorUserId(): int
    {
        return $this->authorUserId;
    }
}