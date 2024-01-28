<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;

final class LogoutController
{
    #[Route('/admin/logout', name: 'admin_logout')]
    public function __invoke()
    {
    }
}
