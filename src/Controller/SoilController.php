<?php

namespace App\Controller;

use App\Entity\Soil;
use App\Form\SoilType;
use App\Repository\SoilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/soil")
 */
class SoilController extends AbstractController
{
    /**
     * @Route("/", name="soil_index", methods={"GET"})
     */
    public function index(SoilRepository $soilRepository): Response
    {
        return $this->render('soil/index.html.twig', [
            'soils' => $soilRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="soil_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $soil = new Soil();
        $form = $this->createForm(SoilType::class, $soil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($soil);
            $entityManager->flush();

            return $this->redirectToRoute('soil_index');
        }

        return $this->render('soil/new.html.twig', [
            'soil' => $soil,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="soil_show", methods={"GET"})
     */
    public function show(Soil $soil): Response
    {
        return $this->render('soil/show.html.twig', [
            'soil' => $soil,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="soil_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Soil $soil): Response
    {
        $form = $this->createForm(SoilType::class, $soil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('soil_index');
        }

        return $this->render('soil/edit.html.twig', [
            'soil' => $soil,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="soil_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Soil $soil): Response
    {
        if ($this->isCsrfTokenValid('delete'.$soil->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($soil);
            $entityManager->flush();
        }

        return $this->redirectToRoute('soil_index');
    }
}
