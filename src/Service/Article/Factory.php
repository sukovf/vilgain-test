<?php

namespace App\Service\Article;

use App\Entity\Article;

class Factory
{
    public function create(): Article
    {
        return new Article();
    }
}