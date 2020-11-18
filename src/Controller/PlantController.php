<?php

namespace App\Controller;

use App\Entity\Attribute;
use App\Entity\AttributeFamily;
use App\Entity\AttributeValue;
use App\Entity\FloweringAndCrop;
use App\Entity\Humus;
use App\Entity\Image;
use App\Entity\Insolation;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantAttribute;
use App\Entity\Port;
use App\Entity\Soil;
use App\Form\PlantType;
use App\Repository\PlantAttributeRepository;
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
        //'insolation','port'
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

        $rusticity = $request->query->get('rusticity');
        if ($rusticity){
            $restricted = true;
            $matched_plants = $this->getDoctrine()->getRepository(Plant::class)->findByRusticity($rusticity);
            $ids = array();
            /** @var Plant $match */
            foreach ($matched_plants as $match){
                $ids[] = $match->getId();
            }
            if (!$restrict['id'])
                $restrict['id'] = $ids;
            else
                $restrict['id'] = array_intersect($restrict['id'],$ids);
        }

        //attributes
        $used_attributes = array();
        $attributes_value = array();
        $excluded_attributes = array();
        foreach ($request->query->all() as $key => $value){
            if (strpos($key,'a_')===0){ //attribute
                $used_attributes[substr($key,2)] = $this->getDoctrine()->getRepository(Attribute::class)->findOneByCode(substr($key,2));
                $attributes_value[substr($key,2)] = $value;
            }
        }

        if ($used_attributes){
            $matched_plants = array();
            /**
             * @var string $v
             * @var Attribute $attribute
             */
            foreach ($used_attributes as $code => $attribute){
                if ($attribute->isTypeNone()) {
                    $av = $this->getDoctrine()->getRepository(AttributeValue::class)->findOneBy(array('value' => null, 'attribute' => $attribute));
                    if ($av) {
                        $av = array($av);
                    }
                }else{
                    if (!is_array($attributes_value[$code])){
                        $attributes_value[$code] = array($attributes_value[$code]);
                    }
                    $av = $this->getDoctrine()->getRepository(AttributeValue::class)->findBy(array('id'=>$attributes_value[$code]));
                }
                $attribute_matched_plants = array();
                if ($av){
                    foreach ($av as $a){
                        foreach ($a->getPlants() as $plant){
                            $attribute_matched_plants[$plant->getId()] = $plant;
                        }
                    }
                    $attributes_value[$code] = $av;
                }
                if (!$attribute_matched_plants){ //oups, no plant for this criteria
                    $excluded_attributes[] = $code;
                    continue;
                }
                if (!$matched_plants){
                    $matched_plants = $attribute_matched_plants;
                }else{
                    $matched_plants = array_intersect_key($matched_plants,$attribute_matched_plants);
                }
            }
            $restricted = true;
            if (!$restrict['id'])
                $restrict['id'] = array_keys($matched_plants);
            else
                $restrict['id'] = array_intersect($restrict['id'],array_keys($matched_plants));
        }
        if (!$restricted)
            unset($restrict['id']);

        $plants = $this->getDoctrine()->getRepository(Plant::class)->findBy(array_merge($filters,$restrict));

        $complex_filters_post_map = array('rusticity');
        foreach ($complex_filters_post_map as $filter){
            if ($request->query->get($filter)){
                $filters[$filter] = $request->query->get($filter);
            }
        }
        $attributes_collection = $this->getDoctrine()->getRepository(Attribute::class)->findAll();
        $attributes = array();
        foreach ($attributes_collection as $attribute){
            $attributes[$attribute->getCode()] = $attribute;
        }

        return $this->render('plant/index.html.twig', [
            'controller_name' => 'PlantController',
            'plants' => $plants,
            'filters' => $filters,
            'attributes' => $attributes,
            'attributes_values' => $attributes_value,
            'excluded_attributes' => $excluded_attributes,
            'query_string' => $s,
            'complex_filters' => $complex_filters,
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
     * @param Plant $plant
     * @param string $slug
     * @return Response
     */
    public function show(Plant $plant,string $slug)
    {
        if ($plant->getSlug() != $slug){
            return $this->redirectToRoute('plant_show',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
        }
        $flowerings = array();
        foreach ($plant->getAttributeValuesByCode('flowering') as $value){
            $flowerings[$value->getCode()] = $value;
        }
        $crops = array();
        foreach ($plant->getAttributeValuesByCode('crop') as $value){
            $crops[$value->getCode()] = $value;
        }
        $root_families = $this->getDoctrine()->getRepository(AttributeFamily::class)->findBy(array('parent'=>null));
        return $this->render('plant/show.html.twig', [
            'families' => $root_families,
            'plant' => $plant,
            'crops' => $crops,
            'flowerings' => $flowerings,
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
