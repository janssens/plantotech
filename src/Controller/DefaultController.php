<?php

namespace App\Controller;

use App\Entity\Insolation;
use App\Entity\Plant;
use App\Entity\Port;
use App\Form\PlantType;
use App\Repository\PlantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {

        return $this->render('home/index.html.twig');
    }
}
