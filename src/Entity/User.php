<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length:180,unique:true)]
    private string $username;

    #[ORM\Column(type:Types::JSON)]
    private $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column(length:255)]
    private string $email;

    #[ORM\Column]
    private bool $is_active;

    #[ORM\Column(length:255,unique:true)]
    private ?string $rp_token;

    #[ORM\Column]
    private ?DateTime $rp_token_created_at;

    #[ORM\Column(length:255,unique:true)]
    private ?string $confirmation_token;

    #[ORM\Column(length:255)]
    private ?string $wordpressID;

    #[ORM\Column(length:255)]
    private $wordpressUsername;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $roles = $this->roles;
        $roles[] = $role;
        $this->roles = array_unique($roles);
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getRpToken(): ?string
    {
        return $this->rp_token;
    }

    public function setRpToken(?string $rp_token): self
    {
        $this->rp_token = $rp_token;

        return $this;
    }

    public function getRpTokenCreatedAt(): ?\DateTimeInterface
    {
        return $this->rp_token_created_at;
    }

    public function getRpDelay(): ?\DateTime
    {
        return date_add($this->rp_token_created_at,date_interval_create_from_date_string('1 day'));
    }

    public function setRpTokenCreatedAt(?\DateTimeInterface $rp_token_created_at): self
    {
        $this->rp_token_created_at = $rp_token_created_at;

        return $this;
    }

    public function generateRpToken(){
        $token = md5(uniqid());
        $this->setRpToken($token);
        $this->setRpTokenCreatedAt(new \DateTime('now'));
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(?string $confirmation_token): self
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }

    public function getWordpressID(): ?string
    {
        return $this->wordpressID;
    }

    public function setWordpressID(?string $wordpressID): self
    {
        $this->wordpressID = $wordpressID;

        return $this;
    }

    public function getWordpressUsername(): ?string
    {
        return $this->wordpressUsername;
    }

    public function setWordpressUsername(?string $wordpressUsername): self
    {
        $this->wordpressUsername = $wordpressUsername;

        return $this;
    }

    static function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-{}#[]=+*!&$%?,;:';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 10; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
