<?php

namespace App\Security;

use App\Entity\User;
use App\Event\UserCreatedFromWordpressOAuthEvent;
use App\Repository\UserRepository;
use App\Service\ConfigService;
use mysql_xdevapi\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WordpressUserProvider implements UserProviderInterface{

    private $config;
    private $userRepository;
    private $urlGenerator;
    private $eventDispatcher;
    private $httpClient;
    private $tokenManager;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserRepository $userRepository,
        ConfigService $config,
        UrlGeneratorInterface $urlGenerator,
        HttpClientInterface $httpClient,
        CsrfTokenManagerInterface $tokenManager
    ){
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->tokenManager = $tokenManager;
    }

    public function loadUserFromWordpressOAuth($code): ?User
    {
        $accessToken = $this->getAccessToken($code);
        $wordpressUserData = $this->getUserInfo($accessToken);

        [
            'user_email' => $email,
            'ID' => $wordpressID,
            'user_login' => $wordpressUsername,
        ] = $wordpressUserData;

        $user = $this->userRepository->getUserFromWordpressOAuth($wordpressID,$wordpressUsername,$email);

        if (!$user){
            $randomPassword = User::randomPassword();
            $user = $this->userRepository->createUserFromWordpressOAuth($wordpressID,$wordpressUsername,$email,$randomPassword);
            $this->eventDispatcher->dispatch(new UserCreatedFromWordpressOAuthEvent($email,$randomPassword), UserCreatedFromWordpressOAuthEvent::SEND_EMAIL_WITH_PASSWORD);
        }

        return $user;
    }

    private function getAccessToken($code): string
    {
        $url = $this->config->getValue('app/oauth_url').'oauth/token';
        $redirect_url = $this->urlGenerator->generate('app_login',array('wordpress-oauth-provider'=>true),UrlGeneratorInterface::ABSOLUTE_URL);

        $curl_post_data =  [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirect_url,
            'state' => $this->tokenManager->getToken('wordpress-oauth-token')->getValue()
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->config->getValue('app/oauth_client_id').':'.$this->config->getValue('app/oauth_client_secret')); //Your credentials goes here
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate

        $curl_response = curl_exec($curl);
        $response = json_decode($curl_response,true);
        curl_close($curl);

        if (isset($response['error'])){
            throw new \Exception('['.$response['error'].'] '.$response['error_description']);
        }

        if (!isset($response['access_token'])||!$response['access_token']){
            throw new ServiceUnavailableHttpException(null,"L'authentification via wordpress a échoué.");
        }

        return $response['access_token'];
    }

    private function getUserInfo(string $accessToken): array
    {

        $url = $this->config->getValue('app/oauth_url').'oauth/me/';
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$accessToken}"
            ]
        ];

        $response = $this->httpClient->request('GET', $url, $options);

        $data = $response->toArray();

        if (!isset($data['user_email'])||!$data['user_email']){
            if (!isset($data['ID'])||!$data['ID']){
                if (!isset($data['user_login'])||!$data['user_login']){
                    throw new ServiceUnavailableHttpException(null,"réponse incohérente");
                }
            }
        }

        return $data;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User || !$user->getWordpressID()){
            throw new UnsupportedUserException();
        }

        $wordpressID = $user->getWordpressID();

        return $this->loadUserByUsername($wordpressID);
    }

    public function loadUserByUsername(string $wordpressID)
    {
        $user = $this->userRepository->findOneBy([
            'wordpressID' => $wordpressID
        ]);

        if (!$user){
            throw new UsernameNotFoundException('Utilisateur inexistant.');
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }


}