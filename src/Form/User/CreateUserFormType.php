<?php

namespace App\Form\User;

use App\Entity\User;
use App\Security\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @extends AbstractType<User>
 */
class CreateUserFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', TextType::class, [
                'mapped'        => false,
                'constraints'   => [
                    new NotBlank(),
                    new Length(['min' => 6])
                ]
            ])
            ->add('name', TextType::class)
            ->add('role', EnumType::class, [
                'class'         => UserRole::class,
                'constraints'   => [new NotNull()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}