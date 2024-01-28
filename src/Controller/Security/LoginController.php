<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;

final class LoginController
{
    public function __construct(
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private AuthenticationUtils $authenticationUtils,
        private Pool $adminPool,
        private TokenStorageInterface $tokenStorage,
        private TranslatorInterface $translator,
        private ?CsrfTokenManagerInterface $csrfTokenManager = null
    ) {
    }

    #[Route('/admin/login', name: 'admin_login')]
    public function __invoke(Request $request): Response
    {
        if ($this->isAuthenticated()) {
            $request->getSession()->getFlashBag()->add(
                'admin_user_error',
                $this->translator->trans('user_already_authenticated')
            );

            return new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));
        }

        $csrfToken = null;
        if (null !== $this->csrfTokenManager) {
            $csrfToken = $this->csrfTokenManager->getToken('authenticate')->getValue();
        }

        return new Response($this->twig->render('Admin/Security/login.html.twig', [
            'admin_pool' => $this->adminPool,
            'csrf_token' => $csrfToken,
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'reset_route' => '', //$this->urlGenerator->generate('sonata_admin_resetting_request'),
        ]));
    }

    private function isAuthenticated(): bool
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return false;
        }

        $user = $token->getUser();

        return $user instanceof UserInterface;
    }
}
