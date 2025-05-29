<?php

namespace App\Tests\Functional\Form\Article;

use App\Entity\Article;
use App\Form\Article\UpdateArticleFormType;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class UpdateArticleFormTypeTest extends KernelTestCase
{
    /** @var FormInterface<Article> */
    private FormInterface $form;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get(FormFactoryInterface::class);

        $article = new Article();
        $this->form = $formFactory->create(UpdateArticleFormType::class, $article);
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
                    'content'   => 'Blah blah blah...'
                ],
                'isValid'       => true
            ],
            'missingTitle'     => [
                'data'      => [
                    'content'   => 'Blah blah blah...'
                ],
                'isValid'       => false
            ],
            'emptyTitle'        => [
                'data'      => [
                    'title'     => '',
                    'content'   => 'Blah blah blah...'
                ],
                'isValid'       => false
            ],
            'missingContent'    => [
                'data'      => [
                    'title'     => 'Foo bar'
                ],
                'isValid'       => false
            ],
            'emptyContent'      => [
                'data'      => [
                    'title'     => 'Foo bar',
                    'content'   => ''
                ],
                'isValid'       => false
            ]
        ];
    }
}