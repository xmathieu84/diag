<?php
 namespace App\Helper;


 use App\Repository\SousDossierRepository;

 trait SousDossierRepoTrait{

     protected SousDossierRepository $sousDossierRepository;

     /**
      * @param SousDossierRepository $sousDossierRepository
      * @required
      */
     public function setSousDossierRepo(SousDossierRepository $sousDossierRepository){
         $this->sousDossierRepository = $sousDossierRepository;
     }
 }
