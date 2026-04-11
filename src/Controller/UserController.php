<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function basket(): Response
    {
        return $this->render('user/basket.html.twig', [
        ]);
    }
    #[Route('/account', name: 'app_account')]
    public function account(): Response
    {
        return $this->render('user/account.html.twig', [
        ]);
    }
}
