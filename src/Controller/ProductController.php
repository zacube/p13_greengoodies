<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/product', name: 'app_product')]
    public function product(): Response
    {
        return $this->render('product/product.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
