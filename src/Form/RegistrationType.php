<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\StrongPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Имя',
                'attr'  => ['placeholder' => 'Иван'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'  => ['placeholder' => 'ivan@example.com'],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Пароли не совпадают',
                'first_options'  => ['label' => 'Пароль'],
                'second_options' => ['label' => 'Повтор пароля'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Пароль обязателен'),
                    new Assert\Length(
                        min: 8,
                        minMessage: 'Пароль не короче 8 символов',
                    ),
                    new StrongPassword(),
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Зарегистрироваться']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}