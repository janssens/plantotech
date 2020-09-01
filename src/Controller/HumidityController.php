<?php

namespace App\Controller;

use App\Entity\Humidity;
use App\Form\HumidityType;
use App\Repository\HumidityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/humidity")
 */
class HumidityController extends AbstractController
{
    /**
     * @Route("/", name="humidity_index", methods={"GET"})
     */
    public function index(HumidityRepository $humidityRepository): Response
    {
        return $this->render('humidity/index.html.twig', [
            'humidities' => $humidityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="humidity_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $humidity = new Humidity();
        $form = $this->createForm(HumidityType::class, $humidity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($humidity);
            $entityManager->flush();

            return $this->redirectToRoute('humidity_index');
        }

        return $this->render('humidity/new.html.twig', [
            'humidity' => $humidity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="humidity_show", methods={"GET"})
     */
    public function show(Humidity $humidity): Response
    {
        return $this->render('humidity/show.html.twig', [
            'humidity' => $humidity,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="humidity_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Humidity $humidity): Response
    {
        $form = $this->createForm(HumidityType::class, $humidity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('humidity_index');
        }

        return $this->render('humidity/edit.html.twig', [
            'humidity' => $humidity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="humidity_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Humidity $humidity): Response
    {
        if ($this->isCsrfTokenValid('delete'.$humidity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($humidity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('humidity_index');
    }
}
