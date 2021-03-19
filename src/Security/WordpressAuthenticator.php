<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class WordpressAuthenticator extends AbstractGuardAuthenticator
{
    private $csrfTokenManager;
    private $urlGenerator;
    private $wordpressUserProvider;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface $urlGenerator,
        WordpressUserProvider $wordpressUserProvider)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
        $this->wordpressUserProvider = $wordpressUserProvider;
    }

    public function supports(Request $request)
    {
        return $request->query->has('wordpress-oauth-provider');
    }

    public function getCredentials(Request $request)
    {
        $state = $request->query->get('state');

        if (!$state || !$this->csrfTokenManager->isTokenValid(new CsrfToken('wordpress-oauth',$state))){
            throw new AccessDeniedException('nop');
        }

        return [
            'code' => $request->query->get('code')
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        if ($credentials === null) {
            return null;
        }

        return $this->wordpressUserProvider->loadUserFromWordpressOAuth($credentials['code']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => 'Authentification refusée'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'message' => 'Authentification requise'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
