<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{
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
    public function signin(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, Security $security): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $manager->persist($user);
            $manager->flush();
            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('auth/signin.html.twig', [
            'form' => $form->createView()
        ], new Response('', $form->isSubmitted() && !$form->isValid() ? 422 : 200));
//          Affichage des messages d'erreur sur les formulaires
//          symfony/ux-turbo intercepte les soumissions de formulaires et les envoie en POST en AJAX(requête fetch).
//          Par défaut, Symfony retourne un code 200, donc Turbo pense que tout est ok et n'affiche pas les messages d'erreur.
//          Il ne les affiche que s'il reçoit un 422.
    }

    #[Route('/logout', name: 'app_auth_logout')]
    public function logout(): void
    {
        // Ce code ne sera jamais exécuté
        throw new \LogicException('This should never be reached!');
    }

}
