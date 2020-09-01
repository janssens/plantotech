<?php

namespace App\Controller;

use App\Entity\Clay;
use App\Form\ClayType;
use App\Repository\ClayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/clay")
 */
class ClayController extends AbstractController
{
    /**
     * @Route("/", name="clay_index", methods={"GET"})
     */
    public function index(ClayRepository $clayRepository): Response
    {
        return $this->render('clay/index.html.twig', [
            'clays' => $clayRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="clay_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $clay = new Clay();
        $form = $this->createForm(ClayType::class, $clay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($clay);
            $entityManager->flush();

            return $this->redirectToRoute('clay_index');
        }

        return $this->render('clay/new.html.twig', [
            'clay' => $clay,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="clay_show", methods={"GET"})
     */
    public function show(Clay $clay): Response
    {
        return $this->render('clay/show.html.twig', [
            'clay' => $clay,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="clay_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Clay $clay): Response
    {
        $form = $this->createForm(ClayType::class, $clay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('clay_index');
        }

        return $this->render('clay/edit.html.twig', [
            'clay' => $clay,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="clay_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Clay $clay): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clay->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($clay);
            $entityManager->flush();
        }

        return $this->redirectToRoute('clay_index');
    }
}
