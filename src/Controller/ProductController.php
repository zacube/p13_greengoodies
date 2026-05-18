<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Form\QuantityType;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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

    #[Route('/product/{slug}', name: 'app_product')]
    public function product(Request $request, EntityManagerInterface $em, BasketRepository $basketRepository, #[MapEntity(mapping: ['slug' => 'slug'])] ?Product $product): Response
    {

        // Produit introuvable → redirection
        if ($product === null) {
            $this->addFlash('erreur', "Produit inexistant");
            return $this->redirectToRoute('app_index');
        }
        // cherche si le produit est déjà dans le panier
        $basketItem = $basketRepository->findOneBy([
            'product' => $product,
            'user' => $this->getUser()
        ]);

        if (!$basketItem) {
            $basketItem = new Basket()->setQuantity(1);
        }

        $isNew = $basketItem->getId() === null; // ← clé de la distinction

        $form = $this->createForm(QuantityType::class, $basketItem, [
            'button_label' => $isNew ? 'Ajouter au panier' : 'Mettre à jour',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_auth_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $newQty = $basketItem->getQuantity();

            if ($isNew && $newQty !== 0) {
                $basketItem
                    ->setUser($this->getUser())
                    ->setProduct($product);
                $em->persist($basketItem);
            } elseif (!$isNew && $newQty === 0) {
                $this->addFlash('success', "Le produit a été retiré du panier");
                $em->remove($basketItem);
            }
            // cas !$isNew && $newQty !== 0 : Doctrine détecte le changement tout seul, rien à faire

            $em->flush();
            return $this->redirectToRoute('app_basket');
        }

        return $this->render('product/product.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
