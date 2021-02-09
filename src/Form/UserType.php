<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    /**
     * buildForm.
     * @param  FormBuilderInterface<User> $builder
     * @param  array<string|mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les deux mots de passe doivent correspondre.',
            'required' => false,
            'first_options' => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
        ])
        ->add('email', EmailType::class, ['label' => 'Adresse email'])
        ->add('roles', ChoiceType::class, [
            'multiple' => true,
            'required' => false,
            'choices' => [
                'USER' => 'ROLE_USER',
                'ADMIN' => 'ROLE_ADMIN',
            ],
        ])
        ;
    }
}
