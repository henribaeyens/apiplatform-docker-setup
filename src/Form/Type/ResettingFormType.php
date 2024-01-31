<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Model\ResetPasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

final class ResettingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'plainPassword',
            RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'form.label.new-password',
                    ],
                ],
                'first_options' => [
                    'label' => 'form.label.new_password'
                ],
                'second_options' => [
                    'label' => 'form.label.password_confirmation'
                ],
                'invalid_message' => 'form.error.passwords_do_not_match',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.error.enter_password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                    ])
                ]
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResetPasswordModel::class
        ]);
    }
}
