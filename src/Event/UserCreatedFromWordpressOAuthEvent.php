<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserCreatedFromWordpressOAuthEvent extends Event
{
    public const SEND_EMAIL_WITH_PASSWORD = 'user_created_from_wordpress_oauth_event.send_email_with_password';

    private $email;
    private $password;

    public function __construct(
        string $email,
        string $password
    )
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}