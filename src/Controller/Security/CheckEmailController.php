<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CheckEmailController extends AbstractController
{
    public function __construct(
        private Pool $adminPool,
        private readonly int $ttl,
    ) {
    }

    #[Route('/admin/check_email/{email}', name: 'admin_check_email')]
    public function __invoke(Request $request, string $email): Response|RedirectResponse
    {
        if (null === $email) {
            return $this->redirectToRoute('admin_password_request');
        }

        return $this->render('Admin/Security/checkEmail.html.twig', [
            'admin_pool' => $this->adminPool,
            'tokenLifetime' => $this->ttl,
        ]);
    }
}