<?php

namespace App\EventSubscriber;

use App\Event\UserCreatedFromWordpressOAuthEvent;
use App\Service\MailService;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class UserCreatedFromWordpressOAuthSubscriber implements EventSubscriberInterface
{
    private $wordpressOAuthLogger;
    private $mailer;

    public function __construct(
        LoggerInterface $wordpressOauthLogger,
        MailService $mailer
    )
    {
        $this->wordpressOAuthLogger = $wordpressOauthLogger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserCreatedFromWordpressOAuthEvent::SEND_EMAIL_WITH_PASSWORD => 'sendEmailWithPassword'
        ];
    }

    public function sendEmailWithPassword(UserCreatedFromWordpressOAuthEvent $event): void
    {
        $email = $event->getEmail();
        $password = $event->getPassword();

        $this->mailer->sendMail($email,'Compte utilisateur créé','register_wordpress',[
            'password' => $password
        ]);

        $this->wordpressOAuthLogger->info("L'utilisateur ayant l'adresse email '{$email}' s'est inscrit via ".
            "wordpress OAuth, un mot de passe aléatoire à modifier lui a été envoyé par email.");
    }
}