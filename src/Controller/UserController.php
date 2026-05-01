<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Repository\BasketRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function basket(BasketRepository $basketRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $basket = $basketRepository->findBy(['user' => ($this->getUser())]);
        dump($basket);

        $totalPrice = 0;
        foreach ($basket as $item) {
            $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $this->render('user/basket.html.twig', [
            'basket' => $basket,
            'totalPrice' => $totalPrice
        ]);
    }

    #[Route('/basket/empty', name: 'app_basket_empty', methods: ['POST'])]
    public function emptyBasket(Request $request, BasketRepository $basketRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if (!$this->isCsrfTokenValid('empty_basket', $request->request->get('_token'))) {
            $this->addFlash('error', 'Votre session a expiré, veuillez réessayer.');
            return $this->redirectToRoute('app_basket');
        }

        $basket = $basketRepository->findBy(['user' => ($this->getUser())]);
        foreach ($basket as $item) {
            $em->remove($item);
            $em->flush();
        }

/*        return $this->render('user/basket.html.twig', [
            'basket' => [],
            'totalPrice' => 0
        ]);*/
        return $this->redirectToRoute('app_basket');
    }

    #[Route('/basket/validate', name: 'app_basket_validate', methods: ['POST'])]
    public function validateBasket(Request $request, BasketRepository $basketRepository, EntityManagerInterface $em): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        // Vérifie le token CSRF du bouton "Valider votre commande" (pattern PRG PostRequestGet)
        if (!$this->isCsrfTokenValid('validate_basket', $request->request->get('_token'))) {
            $this->addFlash('error', 'Votre session a expiré, veuillez réessayer.');
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        // récupère tous les éléments du panier
        $basket = $basketRepository->findBy([
            'user' => $this->getUser(),
        ]);
        if (!$basket) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_account');
        }

        // crée un nouvel achat au nom de l'utilisateur
        $purchase = new Purchase()
            ->setUser($this->getUser());
        // crée un élément d'achat pour chaque élément du panier,
        // et ajoute l'élément d'achat à l'achat
        foreach ($basket as $item) {
            $purchaseItem = new PurchaseItem()
                ->setPurchase($purchase->getId())
                ->setProduct($item->getProduct())
                ->setUnitPrice($item->getProduct()->getPrice())
                ->setQuantity($item->getQuantity());
            $em->persist($purchaseItem);
            $purchase->addPurchaseItem($purchaseItem);
        }
        // stocke l'achat et les éléments d'achat
        $em->persist($purchase);
        $em->flush();

        // vide le panier
        foreach ($basket as $item) {
            $em->remove($item);
            $em->flush();
        }
        // Redirige vers account
        $this->addFlash('success', 'Votre commande a bien été validée !');

        return $this->redirectToRoute('app_account');
    }

    #[Route('/account', name: 'app_account')]
    public function account(BasketRepository $basketRepository, PurchaseRepository $purchaseRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        // récupère tous les achats de l'utilisateur
        $userPurchase = $purchaseRepository->findBy([
            'user' => $this->getUser(),
        ]);
        // crée la liste des commandes
        $purchaseList = [];
        foreach ($userPurchase as $itemP) {
            $totalPrice = 0;
            foreach ($itemP->getPurchaseItem() as $item) {
                $totalPrice += $item->getUnitPrice() * $item->getQuantity();
            }
            $purchaseList[] = [
                'id' => $itemP->getId(),
                'date' => $itemP->getDate(),
                'totalPrice' => $totalPrice
            ];
        }

        return $this->render('user/account.html.twig', [
            'purchaseList' => $purchaseList,
        ]);
    }


    #[Route('/account/delete', name: 'app_account_delete')]
    public function deleteAccount(BasketRepository $basketRepository, PurchaseRepository $purchaseRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $em->remove($this->getUser());
        $em->flush();
        /*dump($this->getUser());*/

        return $this->render('user/account.html.twig', [
            'purchaseList' => [],
        ]);
    }

}
