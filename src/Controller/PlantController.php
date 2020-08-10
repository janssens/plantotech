<?php

namespace App\Controller;

use App\Entity\Insolation;
use App\Entity\Plant;
use App\Entity\PlantsInsolations;
use App\Entity\Port;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlantController extends AbstractController
{
    /**
     * @Route("/plant", name="plant")
     */
    public function index(Request $request)
    {
        $filters = array();
        $restrict = array();
        $complex_filters = array();
        $filters_map = array('life_cycle','root','stratum','drought_tolerance','foliage','leaf_density','limestone');
        foreach ($filters_map as $filter){
            if ($request->query->get($filter)){
                $filters[$filter] = $request->query->get($filter);
            }
        }
        //complex filter (join)
        //'insolation','port'
        if (!isset($restrict['id']))
            $restrict['id'] = array();
        if ($request->query->get('port')){
            /** @var Port $port */
            $port = $this->getDoctrine()->getRepository(Port::class)->find($request->query->get('port'));
            foreach ($port->getPlants() as $plant){
                $restrict['id'][] = $plant->getPlant()->getId();
            }
            $complex_filters['port'] = $request->query->get('port');
        }
        if ($request->query->get('insolation')){
            /** @var Insolation $insolation */
            $insolation = $this->getDoctrine()->getRepository(Insolation::class)->find($request->query->get('insolation'));
            foreach ($insolation->getPlants() as $plant){
                $restrict['id'][] = $plant->getPlant()->getId();
            }
            $complex_filters['insolation'] = $insolation->getType();
        }
        array_unique($restrict['id']);
        if (!$restrict['id'])
            unset($restrict['id']);

        $complex_filters_map = array('family');
        foreach ($complex_filters_map as $filter){
            if ($request->query->get($filter)){
                $filters[$filter] = $request->query->get($filter);
            }
        }
        $plants = $this->getDoctrine()->getRepository(Plant::class)->findBy(array_merge($filters,$restrict));
        return $this->render('plant/index.html.twig', [
            'controller_name' => 'PlantController',
            'plants' => $plants,
            'filters' => $filters,
            'complex_filters' => $complex_filters
        ]);
    }

    /**
     * @Route("/plant/card/{id}/{slug}", name="plant_show")
     */
    public function show(Plant $plant,string $slug)
    {
        if ($plant->getSlug() != $slug){
            return $this->redirectToRoute('plant_show',array('id'=>$plant->getId(),'slug'=>$plant->getSlug()));
        }
        return $this->render('plant/show.html.twig', [
            'plant' => $plant
        ]);
    }
}
