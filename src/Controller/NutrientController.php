<?php

namespace App\Controller;

use App\Entity\Nutrient;
use App\Form\NutrientType;
use App\Repository\NutrientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/nutrient")
 */
class NutrientController extends AbstractController
{
    /**
     * @Route("/", name="nutrient_index", methods={"GET"})
     */
    public function index(NutrientRepository $nutrientRepository): Response
    {
        return $this->render('nutrient/index.html.twig', [
            'nutrients' => $nutrientRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="nutrient_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $nutrient = new Nutrient();
        $form = $this->createForm(NutrientType::class, $nutrient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nutrient);
            $entityManager->flush();

            return $this->redirectToRoute('nutrient_index');
        }

        return $this->render('nutrient/new.html.twig', [
            'nutrient' => $nutrient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="nutrient_show", methods={"GET"})
     */
    public function show(Nutrient $nutrient): Response
    {
        return $this->render('nutrient/show.html.twig', [
            'nutrient' => $nutrient,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="nutrient_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Nutrient $nutrient): Response
    {
        $form = $this->createForm(NutrientType::class, $nutrient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nutrient_index');
        }

        return $this->render('nutrient/edit.html.twig', [
            'nutrient' => $nutrient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="nutrient_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Nutrient $nutrient): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nutrient->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nutrient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('nutrient_index');
    }
}
