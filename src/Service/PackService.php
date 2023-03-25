<?php

namespace App\Service;

use App\Entity\Mission;
use App\Entity\Pack;

class PackService
{
    /**
     * @param array $packs []
     * @param array $missions []
     * @return array []
     */
    public function sortPack(array $packs,array $missions):array{
        $listePack=[];
        foreach ($packs as $pack){
            $presence = true;
            foreach ($pack->getMissions() as $mission){
                if (!in_array($mission,$missions,true)){
                    $presence = false;
                    break;
                }
            }
            if ($presence){
                $listePack[]=$pack;
            }
        }
        return $listePack;
    }
}