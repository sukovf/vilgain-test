<?php

namespace App\Tests\Functional\Form\Article;

use App\Entity\Article;
use App\Form\Article\CreateArticleFormType;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class CreateArticleFormTypeTest extends KernelTestCase
{
    /** @var FormInterface<Article> */
    private FormInterface $form;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get(FormFactoryInterface::class);

        $article = new Article();
        $this->form = $formFactory->create(CreateArticleFormType::class, $article);
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provideData')]
    public function testValidation(array $data, bool $isValid): void
    {
        $this->form->submit($data);

        if ($isValid) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->assertFalse($this->form->isValid());
        }
    }

    /**
     * @return array<string, array{
     *      data: array<string, mixed>,
     *      isValid: bool
     * }>
     */
    public static function provideData(): array
    {
        return [
            'allProperties'     => [
                'data'      => [
                    'title'     => 'Foo bar',
                    'content'   => 'Blah blah blah...',
                    'author_id' => 1
                ],
                'isValid'       => true
            ],
            'missingTitle'     => [
                'data'      => [
                    'content'   => 'Blah blah blah...',
                    'author_id' => 1
                ],
                'isValid'       => false
            ],
            'emptyTitle'        => [
                'data'      => [
                    'title'     => '',
                    'content'   => 'Blah blah blah...',
                    'author_id' => 1
                ],
                'isValid'       => false
            ],
            'missingContent'    => [
                'data'      => [
                    'title'     => 'Foo bar',
                    'author_id' => 1
                ],
                'isValid'       => false
            ],
            'emptyContent'      => [
                'data'      => [
                    'title'     => 'Foo bar',
                    'content'   => '',
                    'author_id' => 1
                ],
                'isValid'       => false
            ],
            'missingAuthorId'   => [
                'data'      => [
                    'title'     => 'Foo bar',
                    'content'   => 'Blah blah blah...',
                ],
                'isValid'       => false
            ]
        ];
    }
}