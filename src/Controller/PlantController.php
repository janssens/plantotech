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
use App\Entity\PlantFamily;
use App\Entity\Port;
use App\Entity\Property;
use App\Entity\PropertyOrAttribute;
use App\Entity\Soil;
use App\Entity\Source;
use App\Form\PlantType;
use App\Repository\PlantAttributeRepository;
use App\Repository\PlantFamilyRepository;
use App\Repository\PlantRepository;
use http\Exception\BadMethodCallException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/plant")
 */
class PlantController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

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
        if ($request->get('family')){
            $filters['family'] = $request->get('family');
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
        if ($request->get('rusticity')){
            $filters['rusticity'] = $request->get('rusticity');
        }

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

            $plant->setAuthor($this->getUser()->getUsername());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plant);
            $entityManager->flush();

            return $this->redirectToRoute('plant_edit',['id'=>$plant->getId(),'slug'=>$plant->getSlug()]);
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
     * @Route("/edit/{id}/{slug}", name="plant_edit")
     * @IsGranted("ROLE_EDIT")
     * @param Plant $plant
     * @param string $slug
     * @return Response
     */
    public function edit(Plant $plant,string $slug)
    {
        if ($plant->getSlug() != $slug){
            return $this->redirectToRoute('plant_edit',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
        }
        $root_families = $this->getDoctrine()->getRepository(AttributeFamily::class)->findBy(array('parent'=>null));
        $plant_families = $this->getDoctrine()->getRepository(PlantFamily::class)->findBy(array(),array('name' => 'ASC'));;
        return $this->render('plant/edit.html.twig', [
            'families' => $root_families,
            'plant_families' => $plant_families,
            'plant' => $plant,
        ]);
    }

    /**
     * @Route("/card/{id}/{slug}", name="plant_show")
     * @param Plant $plant
     * @param string $slug
     * @return Response
     */
    public function card(Plant $plant,string $slug)
    {
        if ($plant->getSlug() != $slug){
            return $this->redirectToRoute('plant_show',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
        }
        $properties_and_attributes = array();
        /** @var PropertyOrAttribute $element */
        foreach ($this->getDoctrine()->getRepository(PropertyOrAttribute::class)->findAll() as $element){
            $properties_and_attributes[$element->getCode()] = $element;
        }
        return $this->render('plant/card.html.twig', [
            'properties_and_attributes' => $properties_and_attributes,
            'plant' => $plant,
        ]);
    }

    /**
     * @Route("/{id}/edit_flowerings_crops", name="plant_flowerings_crops_edit", methods={"POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function flowerings_crops_edit(Request $request, Plant $plant): JsonResponse
    {
        if (!$request->isXmlHttpRequest()){
            return new JsonResponse(array('error'=>true,'message'=>'ajax only'));
        }
        $token = new CsrfToken('plant_flowerings_crops_edit' , $request->request->get('_csrf_token'));
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        //todo find attributeValue by code and add or remove them.
        $em = $this->getDoctrine()->getManager();
        $flowering = $em->getRepository(Attribute::class)->findOneBy(array('code'=>'flowering'));
        $flowering_values = $em->getRepository(AttributeValue::class)->findBy(array('attribute'=>$flowering));
        if ($flowerings = $request->request->get('flowerings')) {
            /** @var AttributeValue $flowering */
            foreach ($flowering_values as $flowering_value) {
                if (in_array($flowering_value->getCode(), array_keys($flowerings))) {
                    $plant->addAttributeValue($flowering_value);
                } else {
                    $plant->removeAttributeValue($flowering_value);
                }
            }
        }
        $crop = $em->getRepository(Attribute::class)->findOneBy(array('code'=>'crop'));
        $crop_values = $em->getRepository(AttributeValue::class)->findBy(array('attribute'=>$crop));
        if ($crops = $request->request->get('crops')){
            /** @var AttributeValue $crop */
            foreach ($crop_values as $crop_value){
                if (in_array($crop_value->getCode(),array_keys($crops))){
                    $plant->addAttributeValue($crop_value);
                }else{
                    $plant->removeAttributeValue($crop_value);
                }
            }
        }
        $em->persist($plant);
        $em->flush();

        return new JsonResponse(array('success'=>true,'message'=>'enregistré'));
    }

    /**
     * @Route("/{id}/new_source", name="plant_source_new", methods={"POST"})
     * @IsGranted("ROLE_EDIT")
     */
    public function source_new(Request $request, Plant $plant): Response
    {
        if (!$request->isXmlHttpRequest()){
            return $this->redirectToRoute('plant_show',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
        }
        $token = new CsrfToken('plant_source_new' , $request->request->get('_csrf_token'));
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $value = $request->request->get('value');
        if ($value){
            $source = new Source();
            $source->setName($value);
            $source->setPlant($plant);
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            $em->flush();
        }
        return $this->render('property/_partial/sources_edit.html.twig', [
            'plant' => $plant
        ]);
    }

    /**
     * @Route("/{id}/delete_source/{source}", name="plant_source_delete", methods={"POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function source_delete(Request $request, Plant $plant,Source $source): Response
    {
        if (!$request->isXmlHttpRequest()){
            return new JsonResponse(array('error'=>true,'message'=>'ajax only'));
        }
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('plant_source_delete_' . $source->getId(), $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            if ($source->getPlant() === $plant){
                $em = $this->getDoctrine()->getManager();
                $em->remove($source);
                $em->flush();
            }
            return new JsonResponse(array('success'=>true,'message'=>'source supprimée'));
        }
    }

    /**
     * @Route("/{id}/edit_source/{source}", name="plant_source_edit", methods={"POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function source_edit(Request $request, Plant $plant,Source $source): Response
    {
        if (!$request->isXmlHttpRequest()){
            return new JsonResponse(array('error'=>true,'message'=>'ajax only'));
        }
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('plant_source_edit_' . $source->getId(), $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            $url = $request->request->get('value');
            if ($plant === $source->getPlant()){
                $em = $this->getDoctrine()->getManager();
                if ($url){
                    $source->setName($url);
                    $em->persist($source);
                }else{
                    $em->remove($source);
                }
                $em->flush();
                return new JsonResponse(array('success'=>true,'message'=>'enregistré'));
            }
            return new JsonResponse(array('error'=>true));
        }
        return new JsonResponse(array());
    }

    /**
     * @Route("/{id}/edit_property/{code}", name="plant_property_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function property_edit(Request $request, Plant $plant,string $code): JsonResponse
    {
        if (!$request->isXmlHttpRequest()){
            return new JsonResponse(array('error'=>true,'message'=>'ajax only'));
        }
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('plant_property_edit_' . $code, $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            //
            $parameters = $request->request->all();
            foreach ($parameters as $key => $value){
                if ($key === '_csrf_token'){
                    continue;
                }
                if (in_array($key,['min_height','max_height','min_width','max_width'])){
                    $value = intval($value);
                }
                $plant->{Property::camelCase("set_".$key)}($value);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($plant);
            $em->flush();
            //
            return new JsonResponse(array('success'=>true,'message'=>'enregistré'));
        }
        return new JsonResponse(array());
    }

    /**
     * @Route("/{id}/edit_attribute/{attribute}", name="plant_attribute_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function attribute_edit(Request $request, Plant $plant,Attribute $attribute): JsonResponse
    {
        if (!$request->isXmlHttpRequest()){
            return new JsonResponse(array('error'=>true,'message'=>'ajax only'));
        }
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('plant_attribute_edit_'.$attribute->getCode(), $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            $em = $this->getDoctrine()->getManager();

            if ($attribute->isTypeMultiple()){
                $values = $request->request->get('value');
                $attributeValues = $this->getDoctrine()->getRepository(AttributeValue::class)->findBy(array('id'=>$values));
                foreach ($attribute->getAvailableValues() as $v){
                    if (in_array($v,$attributeValues)){
                        $plant->addAttributeValue($v);
                    }else{
                        $plant->removeAttributeValue($v);
                    }
                }
            }else if ($attribute->isTypeUnique()){
                $value = $request->request->get('value');
                $v = $plant->getAttributeValuesByCode($attribute->getCode())->first();
                if (!$value && $v){
                    $plant->removeAttributeValue($v);
                }else if($value && !$v){
                    $attributeValue = new AttributeValue();
                    $attributeValue->setAttribute($attribute);
                    $attributeValue->setValue($value);
                    $attributeValue->setCode(Plant::makeSlug($value));
                    $em->persist($attributeValue);
                    $plant->addAttributeValue($attributeValue);
                }else if($value && $v){
                    $v->setValue($value);
                    $em->persist($v);
                }
            }else if ($attribute->isTypeNone()){
                $code = $request->request->get('value');
                $v = $plant->getAttributeValuesByCode($attribute->getCode())->first();
                if (!$code && $v){
                    $plant->removeAttributeValue($v);
                }else if($code && $code == $attribute->getCode() && !$v){
                    $attributeValue = $attribute->getAvailableValues()->first();
                    $plant->addAttributeValue($attributeValue);
                }
            }else{ //type single
                $value = $request->request->get('value');
                $attributeValue = $this->getDoctrine()->getRepository(AttributeValue::class)->find($value);
                foreach ($attribute->getAvailableValues() as $v){
                    if ($v == $attributeValue){
                        $plant->addAttributeValue($v);
                    }else{
                        $plant->removeAttributeValue($v);
                    }
                }
            }

            if ($attribute->getMainValue()){
                $values = $request->request->get('main_value');
                $newMainValues = $this->getDoctrine()->getRepository(AttributeValue::class)->findBy(array('id'=>$values));
                $oldMainValues = $plant->getMainAttributeValuesByCode($attribute->getCode());
                foreach ($oldMainValues as $v){
                    if (!in_array($v,$newMainValues)){
                        $plant->removeAttributeValue($v);
                    }
                }
                foreach ($newMainValues as $v){
                    if (!$oldMainValues->contains($v)){
                        $plant->addAttributeValue($v);
                    }
                }
            }

            $em->persist($plant);
            $em->flush();

            return new JsonResponse(array('success'=>true,'message'=>'enregistré'));
        }

        return new JsonResponse(array());
    }

    /**
     * @Route("/{id}/edit_family/", name="plant_edit_family", methods={"GET","POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function family_edit(Request $request, Plant $plant): JsonResponse
    {
        if (!$request->isXmlHttpRequest()){
            return new JsonResponse(array('error'=>true,'message'=>'ajax only'));
        }
        if ($request->isMethod('POST')) {
            $token = new CsrfToken('plant_edit_family', $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            $id = $request->request->get('value');
            $em = $this->getDoctrine()->getManager();
            if ($id and intval($id)>0){
                $newFamily = $em->getRepository(PlantFamily::class)->find($id);
                $plant->setFamily($newFamily);
                $em->persist($plant);
                $em->flush();
                //
                return new JsonResponse(array('success'=>true,'message'=>'enregistré'));
            }else if ($id === '-1') {
                $plant->setFamily(null);
                $em->persist($plant);
                $em->flush();
                //
                return new JsonResponse(array('success'=>true,'message'=>'enregistré'));
            }else if ($id === '0'){
                $name = strtolower($request->request->get('name'));
                if (!$name){
                    return new JsonResponse(array('error'=>true,'message'=>'Veuillez choisir un nom pour la nouvelle famille','delay'=>5000));
                }
                if (strlen($name)<3){
                    return new JsonResponse(array('error'=>true,'message'=>'Ce nom est trop court','delay'=>5000));
                }
                $exist = $em->getRepository(PlantFamily::class)->findBy(['name'=>$name]);
                if ($exist){
                    return new JsonResponse(array('error'=>true,'message'=>'Cette famille existe déjà !','delay'=>5000));
                }
                $newFamily = new PlantFamily();
                $newFamily->setName($name);
                $em->persist($newFamily);
                $plant->setFamily($newFamily);
                $em->persist($plant);
                $em->flush();
                //
                return new JsonResponse(array('success'=>true,'message'=>'enregistré','data'=>['family_id'=>$newFamily->getId()]));
            }
            //wrong value
            return new JsonResponse(array('error'=>true,'message'=>'oups'));
        }
        return new JsonResponse(array());
    }

    /**
     * @Route("/{id}/new_img", name="plant_img_new", methods={"POST"})
     * @IsGranted("ROLE_EDIT")
     */
    public function image_new(Request $request, Plant $plant): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('plant_show', array('id' => $plant->getId(), 'slug' => $plant->getSlug()));
        }
        $em = $this->getDoctrine()->getManager();
        if ($request->get('refresh')){
            return $this->render('property/_partial/gallery_edit.html.twig', [
                'plant' => $plant
            ]);
        }
        if ($url = $request->get('value')){
            $token = new CsrfToken('plant_img_new', $request->request->get('_csrf_token'));
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            if ($u = Image::urlOk($url)){
                $name = Image::grab_image($u,$this->getParameter('app_images_directory'));
                $img = new Image();
                $img->setName($name);
                $img->setOrigin($u);
                $plant->addImage($img);
                $em->persist($img);
                $em->flush();
            }
            return $this->render('property/_partial/gallery_edit.html.twig', [
                'plant' => $plant
            ]);
        }
        if ($request->files->get('file')){
            // On récupère les images transmises
            $image = $request->files->get('file');

            // On génère un nouveau nom de fichier
            $file = md5(uniqid()).'.'.$image->guessExtension();

            // On copie le fichier dans le dossier uploads
            $upload_success = $image->move(
                $this->getParameter('app_images_directory'),
                $file
            );

            // On crée l'image dans la base de données
            $img = new Image();
            $img->setName($file);
            $plant->addImage($img);
            $em->persist($img);
            $em->flush();
            if( $upload_success ) {
                return $this->json('success', 200);
            } else {
                return $this->json('error', 400);
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="plant_edit_old", methods={"GET","POST"})
     * @IsGranted("ROLE_EDIT")
     */
    public function old_edit(Request $request, Plant $plant): Response
    {
        return $this->redirectToRoute('plant_edit',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
    }

    /**
     * @Route("/{id}", name="plant_delete", methods={"DELETE"})
     * @IsGranted("ROLE_EDIT")
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
     * @Route("{id}/delete/image/{image}", name="plant_delete_image", methods={"POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function deleteImage(Plant $plant, Image $image, Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()){
            return new JsonResponse(array('error'=>true,'message'=>'ajax only'));
        }
        $token = new CsrfToken('plant_delete_image_'.$image->getId(), $request->request->get('_csrf_token'));
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        if ($image->getPlant() === $plant){
            $dir = $this->getParameter('app_images_directory');
            if ($image->getSrc() && file_exists($dir.'/'.$image->getSrc()) && is_file($dir.'/'.$image->getSrc()))
                unlink($dir.'/'.$image->getSrc());
            $plant->removeImage($image);

            $em = $this->getDoctrine()->getManager();

            $em->remove($image);
            $em->persist($plant);
            $em->flush();

            return new JsonResponse(array('success'=>true,'message'=>'image supprimée'));
        }

        return new JsonResponse(array());

    }

}
