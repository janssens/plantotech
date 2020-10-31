<?php

namespace App\Controller;

use App\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin")
     */
    public function admin(Request $request)
    {
        $config = $this->getDoctrine()->getRepository(Config::class)->findAll();
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            /** @var Config $c */
            foreach ($config as $c){
                $new_value = $request->request->get($c->getPath());
                if ($new_value != $c->getValue()){
                    $c->setValue($new_value);
                    $em->persist($c);
                }
            }
            $em->flush();
        }
        return $this->render('admin/index.html.twig',array('config'=>$config));
    }
}
