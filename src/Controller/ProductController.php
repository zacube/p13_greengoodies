<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Form\QuantityType;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function product(Request $request, EntityManagerInterface $em, BasketRepository $basketRepository, ?Product $product): Response
    {
        // Produit introuvable → redirection
        if ($product === null) {
            $this->addFlash('erreur', "Produit inexistant");
            return $this->redirectToRoute('app_index');
        }

        $form = $this->createForm(QuantityType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basketItem = new Basket()
                ->setUser($this->getUser())
                ->setProduct($product)
                ->setQuantity($form->get('quantity')->getData());
            $em->persist($basketItem);
            $em->flush();

            return $this->redirectToRoute('app_basket');

        }

        return $this->render('product/product.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
