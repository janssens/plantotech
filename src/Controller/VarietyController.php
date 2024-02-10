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
use App\Entity\Variety;
use App\Form\PlantType;
use App\Form\VarietyType;
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
 * @Route("/variety")
 */
class VarietyController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/new", name="variety_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $variety = new Variety();
        $form = $this->createForm(VarietyType::class, $variety);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $variety->setAuthor($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($variety);
            $entityManager->flush();

            return $this->redirectToRoute('variety_edit',['id'=>$variety->getId(),'slug'=>$variety->getSlug()]);
        }

        return $this->render('variety/new.html.twig', [
            'variety' => $variety,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="variety_redirect_to_show")
     */
    public function redirectToShow(Variety $variety){
        return $this->redirectToRoute('variety_show',array('id'=>$variety->getId(),'slug'=>$variety->getSlug()));
    }

    /**
     * @Route("/edit/{id}/{slug}", name="variety_edit")
     * @IsGranted("ROLE_EDIT")
     * @param Variety $variety
     * @param string $slug
     * @return Response
     */
    public function edit(Variety $variety,string $slug)
    {
        if ($variety->getSlug() != $slug){
            return $this->redirectToRoute('variety_edit',array('id'=>$variety->getId(),'slug'=>$variety->getSlug()));
        }
/*        $root_families = $this->getDoctrine()->getRepository(AttributeFamily::class)->findBy(array('parent'=>null));
        $plant_families = $this->getDoctrine()->getRepository(PlantFamily::class)->findBy(array(),array('name' => 'ASC'));;
        return $this->render('plant/edit.html.twig', [
            'families' => $root_families,
            'plant_families' => $plant_families,
            'plant' => $plant,
        ]);*/
    }

    /**
     * @Route("/card/{id}/{slug}", name="variety_show")
     * @param Variety $variety
     * @param string $slug
     * @return Response
     */
    public function card(Variety $variety,string $slug)
    {
        if ($variety->getSlug() != $slug){
            return $this->redirectToRoute('plant_show',array('id'=>$variety->getId(),'slug'=>$variety->getSlug()));
        }
        $properties_and_attributes = array();
        /** @var PropertyOrAttribute $element */
        foreach ($this->getDoctrine()->getRepository(PropertyOrAttribute::class)->findAll() as $element){
            $properties_and_attributes[$element->getCode()] = $element;
        }
        return $this->render('plant/card.html.twig', [
            'properties_and_attributes' => $properties_and_attributes,
            'plant' => $variety->getParent(),
            'variety' => $variety,
        ]);
    }

    /**
     * @Route("/{id}/edit_flowerings_crops", name="variety_flowerings_crops_edit", methods={"POST"})
     * @IsGranted("ROLE_EDIT")
     * @return JsonResponse
     */
    public function flowerings_crops_edit(Request $request, Plant $plant): JsonResponse
    {
        /*if (!$request->isXmlHttpRequest()){
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
            /** @var AttributeValue $flowering *//*
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
            /** @var AttributeValue $crop *//*
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
        */
    }

    /**
     * @Route("/{id}/new_source", name="variety_source_new", methods={"POST"})
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
     * @Route("/{id}/delete_source/{source}", name="variety_source_delete", methods={"POST"})
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
     * @Route("/{id}/edit_source/{source}", name="variety_source_edit", methods={"POST"})
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
     * @Route("/{id}/edit_property/{code}", name="variety_property_edit", methods={"GET","POST"})
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
     * @Route("/{id}/edit_attribute/{attribute}", name="variety_attribute_edit", methods={"GET","POST"})
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
     * @Route("/{id}/edit_family/", name="variety_edit_family", methods={"GET","POST"})
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
     * @Route("/{id}/new_img", name="variety_img_new", methods={"POST"})
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
     * @Route("/{id}", name="variety_delete", methods={"DELETE"})
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
     * @Route("{id}/delete/image/{image}", name="variety_delete_image", methods={"POST"})
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
