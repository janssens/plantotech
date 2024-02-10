<?php

namespace App\Controller;

use App\Entity\AttributeFamily;
use App\Entity\Config;
use App\Entity\Plant;
use App\Entity\User;
use App\Service\ConfigService;
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
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

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
                if ($c->getFrontendType() == 'file'){
                    $image = $request->files->get($c->getPath());
                    if ($image){
                        $file = Plant::makeSlug($this->configService->getValue('app/website_name')).'_logo.'.$image->guessExtension();
                        $image->move(
                            $this->getParameter('app_images_directory'),
                            $file
                        );
                        $c->setValue('/uploads/'.$file);
                        $em->persist($c);
                    }
                }else{
                    $new_value = $request->request->get($c->getPath());
                    if ($new_value != $c->getValue()){
                        $c->setValue($new_value);
                        $em->persist($c);
                    }
                }
            }
            $em->flush();
            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'Configuration enregistrée');
        }
        return $this->render('admin/index.html.twig',array('config'=>$config));
    }

    /**
     * @Route("/attributeFamily/", name="attribute_family_edit")
     * @param Request $request
     * @return void
     */
    public function attributeFamilyEdit(Request $request)
    {
        $families = $this->getDoctrine()->getRepository(AttributeFamily::class)->findAll();
        $f = [];
        /** @var AttributeFamily $family */
        foreach ($families as $family)
        {
            if (!$family->getParent()){
                $f[$family->getId()] = array('name' => $family->getName(),'children' => []);
            }else{
                if (!isset($f[$family->getParent()->getId()])){
                    $f[$family->getParent()->getId()] = array('name' => $family->getParent()->getName(),'children' => []);
                }
                $f[$family->getParent()->getId()]['children'][$family->getId()] = array('name' => $family->getName());
            }
        }
        return $this->render('admin/family_attribute.html.twig',array('families'=>$f));
    }

    /**
     * @Route("/users/", name="app_admin_users")
     */
    public function user(Request $request){
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig',array('users'=>$users));
    }
}
