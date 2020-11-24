<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

Class MailService{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(TemplatedEmail $email,int $count = 0){
        try{
            $this->mailer->send($email);
            return 0;
        } catch (TransportExceptionInterface $e) {
            if ($count >= 3){
                return $e;
            }
            // retry
            return $this->sendMail($email,$count++);
        }
    }
}