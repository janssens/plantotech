<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

Class MailService{

    private $mailer;
    private $configService;

    public function __construct(
        MailerInterface $mailer,
        ConfigService $configService
    )
    {
        $this->mailer = $mailer;
        $this->configService = $configService;
    }

    public function sendMail(string $email,string $subject,string $template = 'default',array $context = [],$reply_to = null){
        $emailTemplate = (new TemplatedEmail())
            ->from($this->configService->getValue('app/main_email'))
            ->to($email)
            ->subject('['.$this->configService->getValue('app/website_name').'] '.$subject)
            ->htmlTemplate('emails/'.$template.'.html.twig')
            ->textTemplate('emails/'.$template.'.txt.twig')
            ->context($context);
        if ($reply_to && $reply_to != $email){
            $emailTemplate->replyTo($reply_to);
        }
        return $this->send($emailTemplate,0);
    }

    private function send(TemplatedEmail $email,int $count = 0)
    {
        try{
            $this->mailer->send($email);
            return 0;
        } catch (TransportExceptionInterface $e) {
            if ($count >= 3){
                return $e;
            }
            // retry
            return $this->send($email,$count++);
        }
    }
}