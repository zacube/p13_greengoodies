<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/product/{id}/add', name: 'app_add_product', requirements: ['id' => '\d+'])]
    public function add(EntityManagerInterface $em, BasketRepository $basketRepository, ?Product $product): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        // Produit introuvable → redirection
        if ($product === null) {
            $this->addFlash('erreur', "Produit inexistant");
            return $this->redirectToRoute('app_index');
        }
        $basketItem = new Basket()
            ->setUser($this->getUser())
            ->setProduct($product)
            ->setQuantity(1);

        $em->persist($basketItem);
        $em->flush();

        $basket = $basketRepository->findBy(['user' => ($this->getUser())]);
        dump($basket);

        $totalPrice = 0;
        foreach ($basket as $item) {
            $totalPrice += $item->getProduct()->getPrice();
        }


        return $this->render('user/basket.html.twig', [
            /*'product' => $product,*/
            'basket' => $basket,
            'totalPrice' => $totalPrice
        ]);

    }
}
