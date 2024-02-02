<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Model\ResetPasswordModel;
use App\Model\UserRegistrationModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

final class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'firstName',
                TextType::class, [
                ]
            )
            ->add(
                'lastName',
                TextType::class, [
                ]
            )
            ->add(
                'email',
                EmailType::class, [
                ]
            )
            ->add(
                'password',
                PasswordType::class, [
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationModel::class,
            'csrf_protection' => false
        ]);
    }
}
