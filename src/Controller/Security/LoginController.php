<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[AsController]
final class LoginController extends AbstractController
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly Pool $adminPool,
        private readonly TranslatorInterface $translator,
        private readonly ?CsrfTokenManagerInterface $csrfTokenManager = null
    ) {
    }

    #[Route('/admin/login', name: 'admin_login')]
    public function __invoke(Request $request): Response
    {
        if ($this->isAuthenticated()) {
            $this->addFlash(
                'error',
                $this->translator->trans('security.error.user_already_authenticated')
            );
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        $csrfToken = null;
        if (null !== $this->csrfTokenManager) {
            $csrfToken = $this->csrfTokenManager->getToken('authenticate')->getValue();
        }

        return $this->render('Admin/Security/login.html.twig', [
            'admin_pool' => $this->adminPool,
            'csrf_token' => $csrfToken,
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'reset_route' => $this->generateUrl('admin_password_request'),
        ]);
    }

    private function isAuthenticated(): bool
    {
        return $this->getUser() instanceof UserInterface;
    }
}
