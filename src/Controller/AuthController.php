<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }


    #[Route('/login', name: 'app_auth_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // récupérer l'erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();

        // récupérer le dernier email saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/signin', name: 'app_auth_signin')]
    public function signin(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {


        return $this->render('auth/signin.html.twig', [
        ]);
    }

    #[Route('/logout', name: 'app_auth_logout')]
    public function logout(): void
    {
        // Ce code ne sera jamais exécuté
        throw new \LogicException('This should never be reached!');
    }

}
