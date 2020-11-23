<?php

namespace App\Controller;

use App\Entity\Attribute;
use App\Entity\AttributeFamily;
use App\Entity\AttributeValue;
use App\Entity\FilterCategory;
use App\Entity\FloweringAndCrop;
use App\Entity\Humus;
use App\Entity\Image;
use App\Entity\Insolation;
use App\Entity\Ph;
use App\Entity\Plant;
use App\Entity\PlantAttribute;
use App\Entity\Port;
use App\Entity\PropertyOrAttribute;
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
        $is_ajax = $request->get('_ajax') === '1';
        $filters = array();
        $restricted = false;
        $restrict = array();

        if (!isset($restrict['id']))
            $restrict['id'] = array();

        //
        if ($request->query->get('family')){
            $filters['family'] = $request->get('family');
        }
        if ($request->query->get('rusticity')){
            $filters['rusticity'] = $request->get('rusticity');
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

        $rusticity = $request->get('rusticity');
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
        foreach (array($request->query->all(),$request->request->all()) as $bag){
            foreach ($bag as $key => $value){
                if (!in_array($key,array('q','rusticity','family','_ajax'))){ //attribute
                    $property_or_attribute = $this->getDoctrine()->getRepository(PropertyOrAttribute::class)->findOneBy(array('code'=>$key));
                    if ($property_or_attribute){
                        $used_attributes[$key] = $property_or_attribute;
                        $attributes_value[$key] = $value;
                    }
                }
            }
        }

        if ($used_attributes){
            $matched_plants = array();
            /**
             * @var string $v
             * @var PropertyOrAttribute $attribute
             */
            foreach ($used_attributes as $code => $attribute){
                if ($attribute->isAttribute()){
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
                }else{
                    continue;
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

        $attributes_collection = $this->getDoctrine()->getRepository(Attribute::class)->findAll();
        $attributes = array();
        foreach ($attributes_collection as $attribute){
            $attributes[$attribute->getCode()] = $attribute;
        }

        if ($is_ajax){
            return $this->json(array(
                'success'=>true,
                'data' => array(
                    'filters'=>$this->renderView('plant/_partial/filters.html.twig',array(
                        'filters' => $filters,
                        'attributes_values' => $attributes_value,
                        'excluded_attributes' => $excluded_attributes
                    )),
                    'list'=>$this->renderView('plant/_partial/list.html.twig',array('plants' => $plants,)),
                )
            ));
        }else{
            return $this->render('plant/index.html.twig', [
                'controller_name' => 'PlantController',
                'plants' => $plants,
                'filter_categories' => $this->getDoctrine()->getRepository(FilterCategory::class)->findAll(),
                'filters' => $filters,
                'attributes_values' => $attributes_value,
                'excluded_attributes' => $excluded_attributes,
                'query_string' => $s,
            ]);
        }
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
