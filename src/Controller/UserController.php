<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Repository\BasketRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/basket/empty', name: 'app_empty_basket')]
    public function emptyBasket(BasketRepository $basketRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $basket = $basketRepository->findBy(['user' => ($this->getUser())]);

        foreach ($basket as $item) {
            $em->remove($item);
            $em->flush();
        }

        return $this->render('user/basket.html.twig', [
            'basket' => [],
            'totalPrice' => 0
        ]);
    }

    #[Route('/account', name: 'app_account')]
    public function account(BasketRepository $basketRepository, PurchaseRepository $purchaseRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        // récupère tous les éléments du panier
        $basket = $basketRepository->findBy([
            'user' => $this->getUser(),
        ]);
        if ($basket) {
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
        }

        // récupère tous les achats de l'utilisateur
        $userPurchase = $purchaseRepository->findBy([
            'user' => $this->getUser(),
        ]);

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


    #[Route('/account/del', name: 'app_del_account')]
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
