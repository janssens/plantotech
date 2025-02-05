<?php
// src/Command/ImportCommand.php
namespace App\Command;

use App\Entity\Attribute;
use App\Entity\PlantAttribute;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:user:promote',description: 'Promote an existing user')]
class PromoteUserCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface  $passwordEncoder)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Promote an existing user')
            ->setHelp('This command promote an user')
            ->addArgument('username',  InputArgument::REQUIRED, 'username')
            ->addArgument('role',  InputArgument::REQUIRED, 'role');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(array('username'=>$username));

        if (!$user){
            $output->writeln('user '.$username.' not found');
            return 0;
        }else{
            $user->addRole($role);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return 1;
        }

    }

}
