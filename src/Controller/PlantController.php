<?php

namespace App\Controller;

use App\Entity\FloweringAndCrop;
use App\Entity\Humus;
use App\Entity\Image;
use App\Entity\Insolation;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\Port;
use App\Entity\Soil;
use App\Form\PlantType;
use App\Repository\PlantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plant")
 */
class PlantController extends AbstractController
{
    /**
     * @Route("/index", name="plant_index")
     */
    public function index(Request $request)
    {
        $filters = array();
        $restricted = false;
        $restrict = array();
        $complex_filters = array();
        $filters_map = array('life_cycle','root','stratum','drought_tolerance','foliage','leaf_density','limestone');
        foreach ($filters_map as $filter){
            if ($request->query->get($filter)){
                $filters[$filter] = $request->query->get($filter);
            }
        }
        //complex filter (join)
        //'insolation','port','ph'
        if (!isset($restrict['id']))
            $restrict['id'] = array();
        if ($request->query->get('port')){
            $local_restrict = array();
            /** @var Port $port */
            $port = $this->getDoctrine()->getRepository(Port::class)->find($request->query->get('port'));
            foreach ($port->getPlants() as $plant){
                $local_restrict[] = $plant->getPlant()->getId();
            }
            $complex_filters['port'] = $request->query->get('port');
            if ($restricted){
                $restrict['id'] = array_intersect($restrict['id'],$local_restrict);
            }else{
                $restrict['id'] = $local_restrict;
            }
            $restricted = true;
        }
        if ($request->query->get('flowerings')){
            $local_restrict = array();
            /** @var FloweringAndCrop $flowering */
            $flowerings_month = $request->query->get('flowerings');
            $flowerings = $this->getDoctrine()->getRepository(FloweringAndCrop::class)->findBy(array('month'=>$flowerings_month,'type'=>FloweringAndCrop::TYPE_FLOWERING));
            foreach ($flowerings as $flowering){
                $local_restrict[] = $flowering->getPlant()->getId();
            }
            $complex_filters['flowerings'] = $flowerings_month;
            if ($restricted){
                $restrict['id'] = array_intersect($restrict['id'],$local_restrict);
            }else{
                $restrict['id'] = $local_restrict;
            }
            $restricted = true;        }
        if ($request->query->get('crops')){
            $local_restrict = array();
            /** @var FloweringAndCrop $crop */
            $crops_month = $request->query->get('crops');
            $crops = $this->getDoctrine()->getRepository(FloweringAndCrop::class)->findBy(array('month'=>$crops_month,'type'=>FloweringAndCrop::TYPE_CROP));
            foreach ($crops as $crop){
                $local_restrict[] = $crop->getPlant()->getId();
            }
            $complex_filters['crops'] = $crops_month;
            if ($restricted){
                $restrict['id'] = array_intersect($restrict['id'],$local_restrict);
            }else{
                $restrict['id'] = $local_restrict;
            }
            $restricted = true;        }
        if ($request->query->get('ph')){
            $local_restrict = array();
            /** @var Ph $ph */
            $phs_id = $request->query->get('ph');
            $phs = $this->getDoctrine()->getRepository(Ph::class)->findBy(array('id'=>$phs_id));
            foreach ($phs as $ph){
                foreach ($ph->getPlants() as $plant){
                    $local_restrict[] = $plant->getId();
                }
                if (!key_exists('ph',$complex_filters) || !is_array($complex_filters['ph'])){
                    $complex_filters['ph'] = array();
                }
                $complex_filters['ph'][] = $ph->getId();
            }
            if ($restricted){
                $restrict['id'] = array_intersect($restrict['id'],$local_restrict);
            }else{
                $restrict['id'] = $local_restrict;
            }
            $restricted = true;        }
        if ($request->query->get('insolation')){
            $local_restrict = array();
            /** @var Insolation $insolation */
            $insolations_id = $request->query->get('insolation');
            $insolations = $this->getDoctrine()->getRepository(Insolation::class)->findBy(array('id'=>$insolations_id));
            foreach ($insolations as $insolation){
                foreach ($insolation->getPlants() as $plant){
                    $local_restrict[] = $plant->getPlant()->getId();
                }
                if (!key_exists('insolation',$complex_filters) || !is_array($complex_filters['insolation'])){
                    $complex_filters['insolation'] = array();
                }
                $complex_filters['insolation'][] = $insolation->getType();
            }
            if ($restricted){
                $restrict['id'] = array_intersect($restrict['id'],$local_restrict);
            }else{
                $restrict['id'] = $local_restrict;
            }
            $restricted = true;
        }
        if ($request->query->get('soils')){
            $local_restrict = array();
            /** @var Soil $soil */
            $soils_id = $request->query->get('soils');
            $soils = $this->getDoctrine()->getRepository(Soil::class)->findBy(array('id'=>$soils_id));
            foreach ($soils as $soil){
                foreach ($soil->getPlants() as $plant){
                    $local_restrict[] = $plant->getId();
                }
                if (!key_exists('soils',$complex_filters) || !is_array($complex_filters['soils'])){
                    $complex_filters['soils'] = array();
                }
                $complex_filters['soils'][] = $soil->getName();
            }
            if ($restricted){
                $restrict['id'] = array_intersect($restrict['id'],$local_restrict);
            }else{
                $restrict['id'] = $local_restrict;
            }
            $restricted = true;
        }
        if ($request->query->get('humuses')){
            $local_restrict = array();
            /** @var Humus $humus */
            $humuses_id = $request->query->get('humuses');
            $humuses = $this->getDoctrine()->getRepository(Humus::class)->findBy(array('id'=>$humuses_id));
            foreach ($humuses as $humus){
                foreach ($humus->getPlants() as $plant){
                    $local_restrict[] = $plant->getId();
                }
                if (!key_exists('humuses',$complex_filters) || !is_array($complex_filters['humuses'])){
                    $complex_filters['humuses'] = array();
                }
                $complex_filters['humuses'][] = $humus->getQuantity();
            }
            if ($restricted){
                $restrict['id'] = array_intersect($restrict['id'],$local_restrict);
            }else{
                $restrict['id'] = $local_restrict;
            }
            $restricted = true;
        }
        array_unique($restrict['id']);

        $complex_filters_map = array('family');
        foreach ($complex_filters_map as $filter){
            if ($request->query->get($filter)){
                $filters[$filter] = $request->query->get($filter);
            }
        }
        $s = $request->query->get('q');
        if ($s){
            $restricted = true;
            $matched_names = $this->getDoctrine()->getRepository(Plant::class)->findByString($s);
            $ids = array();
            /** @var Plant $match */
            foreach ($matched_names as $match){
                $ids[] = $match->getId();
            }
            if (!$restrict['id'])
                $restrict['id'] = $ids;
            else
                $restrict['id'] = array_intersect($restrict['id'],$ids);
        }

        if (!$restricted)
            unset($restrict['id']);

        $plants = $this->getDoctrine()->getRepository(Plant::class)->findBy(array_merge($filters,$restrict));


        $insolations = $this->getDoctrine()->getRepository(Insolation::class)->findAll();
        $phs = $this->getDoctrine()->getRepository(Ph::class)->findAll();
        $humuses = $this->getDoctrine()->getRepository(Humus::class)->findAll();
        $soils = $this->getDoctrine()->getRepository(Soil::class)->findAll();

        return $this->render('plant/index.html.twig', [
            'controller_name' => 'PlantController',
            'plants' => $plants,
            'filters' => $filters,
            'query_string' => $s,
            'complex_filters' => $complex_filters,
            'insolations' => $insolations,
            'phs' => $phs,
            'humuses' => $humuses,
            'soils' => $soils,
        ]);
    }

    /**
     * @Route("/new", name="plant_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $plant = new Plant();
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $file = md5(uniqid()).'.'.$image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('app_images_directory'),
                    $file
                );

                // On crée l'image dans la base de données
                $img = new Image();
                $img->setName($file);
                $plant->addImage($img);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plant);
            $entityManager->flush();

            return $this->redirectToRoute('plant_index');
        }

        return $this->render('plant/new.html.twig', [
            'plant' => $plant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plant_redirect_to_show")
     */
    public function redirectToShow(Plant $plant){
        return $this->redirectToRoute('plant_show',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
    }

    /**
     * @Route("/card/{id}/{slug}", name="plant_show")
     */
    public function show(Plant $plant,string $slug)
    {
        if ($plant->getSlug() != $slug){
            return $this->redirectToRoute('plant_show',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
        }
        return $this->render('plant/show.html.twig', [
            'plant' => $plant
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Plant $plant): Response
    {
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $file = md5(uniqid()).'.'.$image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('app_images_directory'),
                    $file
                );

                // On crée l'image dans la base de données
                $img = new Image();
                $img->setName($file);
                $plant->addImage($img);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plant_redirect_to_show',array('id'=>$plant->getId()));
        }

        return $this->render('plant/edit.html.twig', [
            'plant' => $plant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Plant $plant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($plant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plant_index');
    }

    /**
     * @Route("/delete/image/{id}", name="plant_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Image $image, Request $request){
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            $file = $image->getName();
            unlink($this->getParameter('app_images_directory').'/'.$file);

            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

}
