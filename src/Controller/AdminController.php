<?php

namespace App\Controller;

use App\Entity\Config;
use App\Entity\Plant;
use App\Entity\User;
use App\Repository\ConfigRepository;
use App\Service\ConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted as AttributeIsGranted;

#[Route('/admin')]
#[AttributeIsGranted("ROLE_SUPER_ADMIN")]
class AdminController extends AbstractController
{
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    #[Route('/',name:"app_admin")]
    public function admin(Request $request,ConfigRepository $configRepository)
    {
        $config = $configRepository->findAll();
        if ($request->isMethod('POST')) {
            $em = $this->getEntityManager();
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
     * @Route("/users/", name="app_admin_users")
     */
    public function user(Request $request){
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/users.html.twig',array('users'=>$users));
    }
}
