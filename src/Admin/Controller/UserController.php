<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Enum\UserRole;
use App\Filter\JsonListFilter;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends AbstractAdmin<UserController>
 */
final class UserController extends AbstractAdmin
{
    /** @var UserPasswordHasherInterface */
    protected $passwordHasher;

    public function setPasswordHasher(UserPasswordHasherInterface $userPasswordHasherInterface): void
    {
        $this->passwordHasher = $userPasswordHasherInterface;
    }

    protected function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $buttonList = parent::configureActionButtons($buttonList, $action, $object);
        if ($this->isCurrentRoute('edit')) {
            unset($buttonList['create']);
        }

        return $buttonList;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $roles = [];
        foreach (UserRole::cases() as $role) {
            $roles[$role->value] = $role->trans($this->getTranslator());
        }

        $form
            ->tab('General')
                ->with('User Information')
                    ->add('firstName', TextType::class, [
                        'label' => 'form.label.firstname',
                        'required' => false,
                    ])
                    ->add('lastName', TextType::class, [
                        'label' => 'form.label.lastname',
                        'required' => false,
                    ])
                    ->add('email', EmailType::class, [
                        'label' => 'form.label.email',
                    ])
                    ->add('verified', CheckboxType::class, [
                        'label' => 'form.label.verified',
                        'required' => false,
                    ])
                ->end()
            ->end()
            ->tab('Security')
                ->with('Login Information')
                    ->add('plainPassword', PasswordType::class, [
                        'label' => 'form.label.password',
                        'required' => $this->isCurrentRoute('create') ? true : false,
                    ])
                    ->add('roles', ChoiceType::class, [
                        'label' => 'form.label.role',
                        'choices' => array_flip($roles),
                        'multiple' => true,
                        'expanded' => true,
                    ])
                ->end()
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $roles = [];
        foreach (UserRole::cases() as $role) {
            $roles[$role->value] = $role->trans($this->getTranslator());
        }

        $datagrid
            ->add('email', null, [
                'label' => 'form.label.email',
                'show_filter' => true,
                'advanced_filter' => false,
            ])
            ->add('roles', JsonListFilter::class, [
                'label' => 'form.label.role',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => array_flip($roles),
                    'multiple' => false,
                ],
                'show_filter' => true,
                'advanced_filter' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('lastName', null, [
                'label' => 'form.label.lastname',
                'sortable' => false,
            ])
            ->add('firstName', null, [
                'label' => 'form.label.firstname',
                'sortable' => false,
            ])
            ->add('email', null, [
                'label' => 'form.label.email',
                'sortable' => false,
            ])
            ->add('verified', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'form.label.verified',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    // 'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function preUpdate(object $user): void
    {
        /* @var UserInterface $user */
        if (null !== $user->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);
        }
    }

    protected function prePersist(object $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);
        }
    }
}
