<?php

namespace App\Controller;

use App\Entity\Humus;
use App\Form\HumusType;
use App\Repository\HumusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/humus")
 */
class HumusController extends AbstractController
{
    /**
     * @Route("/", name="humus_index", methods={"GET"})
     */
    public function index(HumusRepository $humusRepository): Response
    {
        return $this->render('humus/index.html.twig', [
            'humuses' => $humusRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="humus_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $humu = new Humus();
        $form = $this->createForm(HumusType::class, $humu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($humu);
            $entityManager->flush();

            return $this->redirectToRoute('humus_index');
        }

        return $this->render('humus/new.html.twig', [
            'humu' => $humu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="humus_show", methods={"GET"})
     */
    public function show(Humus $humu): Response
    {
        return $this->render('humus/show.html.twig', [
            'humu' => $humu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="humus_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Humus $humu): Response
    {
        $form = $this->createForm(HumusType::class, $humu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('humus_index');
        }

        return $this->render('humus/edit.html.twig', [
            'humu' => $humu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="humus_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Humus $humu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$humu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($humu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('humus_index');
    }
}
