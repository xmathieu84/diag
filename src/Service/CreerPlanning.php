<?php


namespace App\Service;


use App\Entity\Planning;
use App\Helper\EntityManagerTrait;

class CreerPlanning
{
    use EntityManagerTrait;
    public function horaire($id, $respository,$contenu){


        $agent = $respository->findOneBy(['id'=>$id]);

        if (!$agent->getPlanning()){
            $planning = new Planning();

        }
        else{
            $planning = $agent->getPlanning();

        }
        $planning->setHoraires($contenu);
        $agent->setPlanning($planning);
        $this->manager->persist($planning);
        $this->manager->flush();

        return $contenu;
    }
}