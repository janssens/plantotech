<?php
// src/Command/ImportCommand.php
namespace App\Command;

use App\Entity\Attribute;
use App\Entity\AttributeValue;
use App\Entity\Clay;
use App\Entity\FloweringAndCrop;
use App\Entity\Humidity;
use App\Entity\Humus;
use App\Entity\Insolation;
use App\Entity\InterestType;
use App\Entity\Nutrient;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantFamily;
use App\Entity\PlantsInsolations;
use App\Entity\PlantsPorts;
use App\Entity\Port;
use App\Entity\Soil;
use App\Entity\Source;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NewUserCommand extends Command
{
// the name of the command (the part after "bin/console")
protected static $defaultName = 'app:user:new';

    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface  $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('create a new user')
            ->setHelp('This command create an user')
            ->addArgument('username',  InputArgument::REQUIRED, 'username')
            ->addArgument('email',  InputArgument::REQUIRED, 'email')
            ->addArgument('password',  InputArgument::REQUIRED, 'password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setRoles(array('ROLE_USER'));
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $password
        ));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return 1;
    }

}
