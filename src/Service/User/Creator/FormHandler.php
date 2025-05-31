<?php

namespace App\Service\User\Creator;

use App\Form\User\CreateUserFormType;
use App\Service\Form\ErrorSerializer;
use App\Service\User\Creator\Exception\LogicException;
use App\Service\User\Exception\CreateUserBadRequestException;
use App\Service\User\Factory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandler
{
    public function __construct(
        private readonly Factory              $userFactory,
        private readonly FormFactoryInterface $formFactory,
        private readonly ErrorSerializer      $formErrorSerializer,
        private readonly HandlerOutputFactory $handlerOutputFactory
    ) {}

    public function handle(Request $request): HandlerOutput
    {
        $newUser = $this->userFactory->create();

        $form = $this->formFactory->create(CreateUserFormType::class, $newUser);
        $form->submit($request->toArray());

        if (!$form->isValid()) {
            throw new CreateUserBadRequestException($this->formErrorSerializer->serialize($form));
        }

        $plainTextPassword = $form->get('password')->getData();
        if (!is_string($plainTextPassword)) {
            throw new LogicException('The password must be a string');
        }

        return $this->handlerOutputFactory->create($newUser, $plainTextPassword);
    }
}