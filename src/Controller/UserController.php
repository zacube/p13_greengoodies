<?php

namespace App\Controller;

use App\Repository\BasketRepository;
use Doctrine\ORM\EntityManager;
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
            $totalPrice += ($item->getProduct()->getPrice()) * ($item->getQuantity());
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
    public function account(): Response
    {
        return $this->render('user/account.html.twig', [
        ]);
    }
}
