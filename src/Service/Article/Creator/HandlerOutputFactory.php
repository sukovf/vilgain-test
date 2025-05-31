<?php

namespace App\Service\Article\Creator;

use App\Entity\Article;

class HandlerOutputFactory
{
    public function create(Article $article, int $authorUserId): HandlerOutput
    {
        return new HandlerOutput($article, $authorUserId);
    }
}