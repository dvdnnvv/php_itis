<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('name', TextType::class, ['label' => 'Имя'])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Пароль',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Зарегистрироваться']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true, // Включаем защиту
            'csrf_field_name' => '_token', // Имя поля с токеном
            'csrf_token_id'   => 'registration_item', // Уникальный ID
        ]);
    }
}