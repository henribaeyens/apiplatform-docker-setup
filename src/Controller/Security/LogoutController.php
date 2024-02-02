<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class LogoutController
{
    #[Route('/admin/logout', name: 'admin_logout')]
    public function __invoke()
    {
    }
}
