<?php

namespace App\Tests\Functional\Form\User;

use App\Entity\User;
use App\Form\User\UpdateUserFormType;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class UpdateUserFormTypeTest extends KernelTestCase
{
    /** @var FormInterface<User> */
    private FormInterface $form;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get(FormFactoryInterface::class);

        $user = new User();
        $this->form = $formFactory->create(UpdateUserFormType::class, $user);
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
                    'email'     => 'pepa@zdepa.cz',
                    'name'      => 'Joe',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => true
            ],
            'missingEmail'      => [
                'data'      => [
                    'name'      => 'Joe',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => true
            ],
            'missingName'       => [
                'data'      => [
                    'email'     => 'pepa@zdepa.cz',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => true
            ],
            'missingRole'       => [
                'data'      => [
                    'email'     => 'pepa@zdepa.cz',
                    'name'      => 'Joe',
                ],
                'isValid'       => true
            ],
            'invalidEmail'      => [
                'data'      => [
                    'email'     => 'obviously invalid email'
                ],
                'isValid'       => false
            ],
            'invalidRole'       => [
                'data'      => [
                    'role'      => 'obviously invalid role'
                ],
                'isValid'       => false
            ],
        ];
    }
}