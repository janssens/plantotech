<?php
// src/Command/ImportCommand.php
namespace App\Command;

use App\Entity\Athlete;
use App\Entity\Attribute;
use App\Entity\Club;
use App\Entity\Image;
use App\Entity\Ligue;
use App\Entity\Outsider;
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
use App\Entity\Registration;
use App\Entity\Soil;
use App\Entity\Source;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ImagesImportCommand extends CsvCommand
{
// the name of the command (the part after "bin/console")
protected static $defaultName = 'app:images:import';

    protected function configure()
    {
        $this
            ->setDescription('Import images using a csv')
            ->setHelp('This command populate db with images given as urls in a csv')
            ->addArgument('file',  InputArgument::REQUIRED, 'csv file')
            ->addOption('map','m',InputOption::VALUE_OPTIONAL,'map','')
            ->addOption('delimiter','d',InputOption::VALUE_OPTIONAL,'csv delimiter',';')
            ->addOption('limit','l',InputOption::VALUE_OPTIONAL,'limit')
            ->addOption('start','s',InputOption::VALUE_OPTIONAL,'start at',0)
            ->addOption('default_mapping',null,InputOption::VALUE_NONE,'');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $file = $input->getArgument('file');
        $delimiter = $input->getOption('delimiter');
        $map = $input->getOption('map');
        $limit = $input->getOption('limit');
        $start = $input->getOption('start');
        $default_mapping = $input->getOption('default_mapping');

        $output->writeln([
            '====================================',
            'Import registrations data from a csv',
            '====================================',
        ]);

        $this->setNeededFields(array(
            'latin_name' => array('label' => 'nom latin','index'=>2),
            'first_image' => array('label' => 'première colonne image','index'=>3),
            'last_image' => array('label' => 'dernière colonne image','index'=>4),
        ));

        if (!$map && $this->loadSavedMap($file)){

        }else{
            $this->checkDelimiter($file,$delimiter,$input,$output);
            if (!$default_mapping&&!$map) {
                $this->mapField($file, $input, $output);
                $this->saveMap($file);
            }else{
                $this->setMap(explode(',',$map));
            }
            $confirm = new ConfirmationQuestion('Save this map as default ?', true);
            if ($this->getHelper('question')->ask($input, $output, $confirm)) {
                $this->saveMap($file);
            }
        }
        $output->writeln("<info>MAP : </info>");
        $this->displayMap($output);

        $lines = $this->getLines($file) - 1;

        $output->writeln("<info>File with <fg=cyan>$lines</> lines</info>");
        if ($start<0 or $start > $lines) {
            $start = 0;
        }
        if ($limit){
            $output->writeln("<info>Deal with <fg=cyan>$limit</> lines</info>");
        }
        if ($start != 0){
            $output->writeln("<info>Starting at <fg=cyan>$start</></info>");
        }

        $progress = new ProgressBar($output);
        $progress->setMaxSteps($lines);
        $progress->advance($start);

        if (($handle = fopen($file, "r")) !== FALSE) {

            $processed = 0;
            $goto = $start;
            while ((--$goto > 0) && (fgets($handle, 10000) !== FALSE)) {
            }

            $row = $start;

            while (($data = fgetcsv($handle, 10000, $this->getDelimiter())) !== FALSE) {
                if ($limit and $processed >= $limit) {
                    break;
                }
                if ($row > $lines) {
                    break;
                }

                $start = $this->getNeededFields()['first_image']['index'];
                $end = $this->getNeededFields()['last_image']['index'];

                $data = array_map("utf8_encode", $data); //utf8
                if ($row > 0) { //skip first line
                    $nom_latin = utf8_decode($this->getField('latin_name', $data));
                    $plant = $this->getEntityManager()->getRepository(Plant::class)->findOneBy(array('latin_name'=>$nom_latin));
                    if ($plant){
                        $output->writeln("Yes found ".$plant->getName(), OutputInterface::VERBOSITY_VERBOSE);
                        for ($i = $start;$i <= $end;$i++){
                            if (isset($data[$i]) && strlen($data[$i]) > 5){
                                $output->writeln($i.' : ', OutputInterface::VERBOSITY_VERBOSE);
                                $output->writeln($data[$i], OutputInterface::VERBOSITY_VERBOSE);

                                if ($u = Image::urlOk($data[$i])){
                                    $name = Image::grab_image($u,$this->getParameterBag()->get('app_images_directory'));
                                    $img = new Image();
                                    $img->setName($name);
                                    $img->setOrigin($u);
                                    $plant->addImage($img);
                                    $this->getEntityManager()->persist($img);
                                }

                            }
                        }
                    }else{
                        $output->writeln($nom_latin.' not found', OutputInterface::VERBOSITY_NORMAL);
                    }
                }
                $row++;
                $processed++;
                $progress->advance();
            }
            fclose($handle);

            $this->getEntityManager()->flush();
            $output->writeln("", OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln("flushing . . .", OutputInterface::VERBOSITY_VERBOSE);
            $progress->finish();
        }
        $output->writeln('🙂 done');

        return 1;
    }

}
