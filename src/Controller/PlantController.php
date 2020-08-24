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
            $insolations_id = $request->query->get('insolation');
            $insolations = $this->getDoctrine()->getRepository(Insolation::class)->findBy(array('id'=>$insolations_id));
            foreach ($insolations as $insolation){
                foreach ($insolation->getPlants() as $plant){
                    $restrict['id'][] = $plant->getPlant()->getId();
                }
                if (!key_exists('insolation',$complex_filters) || !is_array($complex_filters['insolation'])){
                    $complex_filters['insolation'] = array();
                }
                $complex_filters['insolation'][] = $insolation->getType();
            }
        }
        array_unique($restrict['id']);

        $complex_filters_map = array('family');
        foreach ($complex_filters_map as $filter){
            if ($request->query->get($filter)){
                $filters[$filter] = $request->query->get($filter);
            }
        }
        $s = $request->query->get('q');
        if ($s){
            $matched_names = $this->getDoctrine()->getRepository(Plant::class)->findByString($s);
            $ids = array();
            /** @var Plant $match */
            foreach ($matched_names as $match){
                $ids[] = $match->getId();
            }
            if (!$restrict['id'])
                $restrict['id'] = $ids;
            else
                $restrict['id'] = array_intersect($restrict['id'],$ids);
        }

        if (!$restrict['id'])
            unset($restrict['id']);

        $plants = $this->getDoctrine()->getRepository(Plant::class)->findBy(array_merge($filters,$restrict));


        $insolations = $this->getDoctrine()->getRepository(Insolation::class)->findAll();

        return $this->render('plant/index.html.twig', [
            'controller_name' => 'PlantController',
            'plants' => $plants,
            'filters' => $filters,
            'query_string' => $s,
            'complex_filters' => $complex_filters,
            'insolations' => $insolations
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
