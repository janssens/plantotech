<?php

namespace App\Controller;

use App\Entity\PlantFamily;
use App\Form\PlantFamilyType;
use App\Repository\PlantFamilyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plant/family")
 */
class PlantFamilyController extends AbstractController
{
    /**
     * @Route("/", name="plant_family_index", methods={"GET"})
     */
    public function index(PlantFamilyRepository $plantFamilyRepository): Response
    {
        return $this->render('plant_family/index.html.twig', [
            'plant_families' => $plantFamilyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="plant_family_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $plantFamily = new PlantFamily();
        $form = $this->createForm(PlantFamilyType::class, $plantFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plantFamily);
            $entityManager->flush();

            return $this->redirectToRoute('plant_family_index');
        }

        return $this->render('plant_family/new.html.twig', [
            'plant_family' => $plantFamily,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plant_family_show", methods={"GET"})
     */
    public function show(PlantFamily $plantFamily): Response
    {
        return $this->render('plant_family/show.html.twig', [
            'plant_family' => $plantFamily,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plant_family_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PlantFamily $plantFamily): Response
    {
        $form = $this->createForm(PlantFamilyType::class, $plantFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plant_family_index');
        }

        return $this->render('plant_family/edit.html.twig', [
            'plant_family' => $plantFamily,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plant_family_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PlantFamily $plantFamily): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plantFamily->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($plantFamily);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plant_family_index');
    }
}
