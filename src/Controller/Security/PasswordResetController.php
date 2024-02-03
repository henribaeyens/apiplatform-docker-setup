<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Form\Type\ResettingFormType;
use App\Model\ResetPasswordModel;
use App\Repository\UserRepository;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
final class PasswordResetController extends AbstractController
{
    public function __construct(
        private readonly Pool $adminPool,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly TranslatorInterface $translator,
        private readonly int $ttl,
    ) {
    }

    #[Route('/admin/password_reset/{token}', name: 'admin_password_reset')]
    public function __invoke(Request $request, string $token): Response|RedirectResponse
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        $user = $this->userRepository->findOneByRecoveryToken($token);

        if (null === $user) {
            throw $this->createNotFoundException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isRecoveryRequestExpired($this->ttl * 3600)) {
            return $this->redirectToRoute('admin_password_request');
        }

        $form = $this->createForm(ResettingFormType::class, new ResetPasswordModel());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $form->getData()->getPlainPassword()));
            $user->setRecoveryToken(null);
            $user->setRecoveryRequestDate(null);
            $this->userRepository->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('flash.success.password_reset')
            );

            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        return $this->render('Admin/Security/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
            'admin_pool' => $this->adminPool,
        ]);
    }
}
