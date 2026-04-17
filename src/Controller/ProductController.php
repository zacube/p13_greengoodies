<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}', name: 'app_product', requirements: ['id' => '\d+'])]
    public function product(?Product $product): Response
    {
        // Produit introuvable → redirection
        if ($product === null) {
            $this->addFlash('erreur', "Produit inexistant");
            return $this->redirectToRoute('app_index');
        }
        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }
}
