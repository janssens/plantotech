<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertyOrAttribute;
use App\Form\PropertyOrAttributeType;
use App\Repository\AttributeRepository;
use App\Repository\PropertyRepository;
use App\Service\ConfigService;
use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/property_attribute")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class PropertyOrAttributeController extends AbstractController
{

    private $csrfTokenManager;
    private $mailer;
    private $config;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        MailService $mailer,
        ConfigService $configService
    )
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->mailer = $mailer;
        $this->config = $configService;
    }

    /**
     * @Route("/", name="property_attribute_index")
     */
    public function index(PropertyRepository $propertyRepository,AttributeRepository $attributeRepository)
    {
        return $this->render('property_attribute/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
            'attributes' => $attributeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="property_attribute_show", methods={"GET"})
     */
    public function show(PropertyOrAttribute $propertyOrAttribute): Response
    {
        return $this->render('property_attribute/show.html.twig', [
            'propertyOrAttribute' => $propertyOrAttribute,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="property_attribute_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PropertyOrAttribute $propertyOrAttribute): Response
    {
        $form = $this->createForm(PropertyOrAttributeType::class, $propertyOrAttribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('property_attribute_index');
        }

        return $this->render('property_attribute/edit.html.twig', [
            'propertyOrAttribute' => $propertyOrAttribute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="property_attribute_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PropertyOrAttribute $propertyOrAttribute): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyOrAttribute->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($propertyOrAttribute);
            $entityManager->flush();
        }

        return $this->redirectToRoute('property_index');
    }

}
