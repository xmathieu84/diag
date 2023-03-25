<?php
 namespace App\Helper;


 use App\Repository\AppelOffreRepository;

 trait AppelOffreRepoTrait{

     protected AppelOffreRepository $appelOffreRepository;

     /**
      * @param AppelOffreRepository $appelOffreRepository
      * @required
      */
     public function setAppelOffreRepo(AppelOffreRepository $appelOffreRepository){
         $this->appelOffreRepository=$appelOffreRepository;
     }
 }