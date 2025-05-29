<?php

namespace App\Service\Article;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\Article\Exception\ArticleNotFoundException;

class Getter
{
    public function __construct(private readonly ArticleRepository $articleRepository) {}

    /**
     * @return Article[]
     */
    public function getAll(): array
    {
        return $this->articleRepository->findAll();
    }

    public function getOne(int $id): Article
    {
        if (!$article = $this->articleRepository->find($id)) {
            throw new ArticleNotFoundException('Article not found');
        }

        return $article;
    }
}