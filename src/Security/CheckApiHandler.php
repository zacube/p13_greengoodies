<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

readonly class CheckApiHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private AuthenticationSuccessHandlerInterface $lexikHandler) {

    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser(); // donne l'utilisateur connecté

        if (!$user->isApi()) {
            return new JsonResponse(['code' => '403','message' => 'Accès API non déverrouillé: '],
                Response::HTTP_FORBIDDEN );
        }

        return $this->lexikHandler->onAuthenticationSuccess($request, $token);
    }
}
