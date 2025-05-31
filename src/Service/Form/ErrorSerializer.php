<?php

namespace App\Service\Form;

use Symfony\Component\Form\FormInterface;

class ErrorSerializer
{
    /**
     * @template T
     * @param FormInterface<T> $form
     */
    public function serialize(FormInterface $form): string
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $path = $error->getOrigin()?->getName() ?? 'form';
            $errors[] = sprintf('%s: %s', $path, $error->getMessage());
        }

        return implode(', ', $errors);

    }
}