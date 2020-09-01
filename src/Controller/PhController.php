<?php

namespace App\Controller;

use App\Entity\Ph;
use App\Form\PhType;
use App\Repository\PhRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ph")
 */
class PhController extends AbstractController
{
    /**
     * @Route("/", name="ph_index", methods={"GET"})
     */
    public function index(PhRepository $phRepository): Response
    {
        return $this->render('ph/index.html.twig', [
            'phs' => $phRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ph_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ph = new Ph();
        $form = $this->createForm(PhType::class, $ph);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ph);
            $entityManager->flush();

            return $this->redirectToRoute('ph_index');
        }

        return $this->render('ph/new.html.twig', [
            'ph' => $ph,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ph_show", methods={"GET"})
     */
    public function show(Ph $ph): Response
    {
        return $this->render('ph/show.html.twig', [
            'ph' => $ph,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ph_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ph $ph): Response
    {
        $form = $this->createForm(PhType::class, $ph);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ph_index');
        }

        return $this->render('ph/edit.html.twig', [
            'ph' => $ph,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ph_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ph $ph): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ph->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ph);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ph_index');
    }
}
