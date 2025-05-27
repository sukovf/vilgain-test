<?php

namespace App\Tests\Functional\Form\User;

use App\Entity\User;
use App\Form\User\CreateUserFormType;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class CreateUserFormTypeTest extends KernelTestCase
{
    /** @var FormInterface<User> */
    private FormInterface $form;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get(FormFactoryInterface::class);

        $user = new User();
        $this->form = $formFactory->create(CreateUserFormType::class, $user);
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
                    'password'  => 'abcdabcd',
                    'name'      => 'Joe',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => true
            ],
            'missingEmail'      => [
                'data'      => [
                    'password'  => 'abcdabcd',
                    'name'      => 'Joe',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => false
            ],
            'missingPassword'   => [
                'data'      => [
                    'email'     => 'pepa@zdepa.cz',
                    'name'      => 'Joe',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => false
            ],
            'missingName'       => [
                'data'      => [
                    'email'     => 'pepa@zdepa.cz',
                    'password'  => 'abcdabcd',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => false
            ],
            'missingRole'       => [
                'data'      => [
                    'email'     => 'pepa@zdepa.cz',
                    'password'  => 'abcdabcd',
                    'name'      => 'Joe',
                ],
                'isValid'       => false
            ],
            'invalidEmail'      => [
                'data'      => [
                    'email'     => 'obviously invalid email',
                    'password'  => 'abcdabcd',
                    'name'      => 'Joe',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => false
            ],
            'shortPassword'     => [
                'data'      => [
                    'email'     => 'pepa@zdepa.cz',
                    'password'  => 'foo',
                    'name'      => 'Joe',
                    'role'      => 'ROLE_ADMIN'
                ],
                'isValid'       => false
            ],
            'invalidRole'       => [
                'data'      => [
                    'email'     => 'pepa@zdepa.cz',
                    'password'  => 'abcdabcd',
                    'name'      => 'Joe',
                    'role'      => 'obviously invalid role'
                ],
                'isValid'       => false
            ],
        ];
    }
}