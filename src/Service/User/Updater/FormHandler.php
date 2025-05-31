<?php

namespace App\Service\User\Updater;

use App\Entity\User;
use App\Form\User\UpdateUserFormType;
use App\Service\Form\ErrorSerializer;
use App\Service\User\Exception\UpdateUserBadRequestException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ErrorSerializer      $formErrorSerializer
    ) {}

    public function handle(User $user, Request $request): User
    {
        $form = $this->formFactory->create(UpdateUserFormType::class, $user);
        $form->submit($request->toArray(), false);

        if (!$form->isValid()) {
            throw new UpdateUserBadRequestException($this->formErrorSerializer->serialize($form));
        }

        return $user;
    }
}