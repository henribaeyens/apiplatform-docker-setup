<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\UserInterface;
use App\Form\Type\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
final class PasswordRequestController extends AbstractController
{
    public function __construct(
        private readonly Pool $adminPool,
        private readonly Mailer $mailer,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly int $ttl,
    ) {
    }

    #[Route('/admin/password_request', name: 'admin_password_request')]
    public function __invoke(Request $request): Response|RedirectResponse
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (string) $form->get('email')->getData();
            /** @var UserInterface|null $user */
            $user = $this->userRepository->findOneAdminByEmail($email);

            if (null !== $user) {
                if ($user->isRecoveryRequestExpired($this->ttl * 3600)) {
                    $this->addFlash(
                        'error',
                        $this->translator->trans('flash.error.token_lifespan_not_exceeded', ['%tokenLifetime%' => $this->ttl])
                    );

                    return $this->redirectToRoute('admin_password_request');
                }

                if (null === $user->getRecoveryToken()) {
                    $user->setRecoveryToken($this->tokenGenerator->generateToken());
                }

                $this->mailer->sendResettingEmailMessage($user);
                $user->setRecoveryRequestDate(new \DateTime());
                $this->userRepository->save($user);

                return $this->redirectToRoute('admin_check_email', [
                    'email' => $email,
                ]);
            } else {
                $this->addFlash(
                    'error',
                    $this->translator->trans('flash.error.no_such_user')
                );
            }
        }

        return $this->render('Admin/Security/request.html.twig', [
            'admin_pool' => $this->adminPool,
            'form' => $form->createView(),
        ]);
    }
}
