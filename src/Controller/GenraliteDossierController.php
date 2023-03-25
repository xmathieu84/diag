<?php

namespace App\Controller;


use App\Helper\DocSouDossierRepoTrait;
use App\Helper\DossierRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GenraliteDossierController extends AbstractController
{
    use DocSouDossierRepoTrait,RequestTrait,EntityManagerTrait,DossierRepoTrait;

    /**
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws \Exception
     * @Route ("/supprimerFichier")
     */
        public function supprimerFichier(DefinirDate $definirDate):JsonResponse{
            $doc = $this->docSousDossierRepository->findOneBy(['id'=>$this->request->getContent()]);
            $agent = $this->getUser()->getAgent();
            $dossier = $doc->getSousDossier()->getDossier();
            $dossier->setNomModifiant($agent->getCivilite()->getPrenom().' '.$agent->getCivilite()->getNom())
                ->setDateModif($definirDate->aujourdhui())
                ->setTypeModif('Suppression du document '.$doc->getLibelle());
            foreach ($doc->getAnnotations() as $annotation){
                $this->manager->remove($annotation);
            }
            $this->manager->persist($dossier);
            $this->manager->remove($doc);
            $this->manager->flush();
            return new JsonResponse();
        }

    /**
     * @return JsonResponse
     * @Route("/suppressionDossier")
     */
        public function suppressionDossier():JsonResponse{
            $dossier = $this->dossierRepository->findOneBy(['id'=>$this->request->getContent()]);
            foreach ($dossier->getSousDossiers() as $sousDossier){
                foreach ($sousDossier->getDocSousDossiers() as $docSousDossier){
                    foreach ($docSousDossier->getAnnotations() as $annotation){
                        $this->manager->remove($annotation);
                    }
                    $this->manager->remove($docSousDossier);
                }
                $this->manager->remove($sousDossier);
            }
            $this->manager->remove($dossier);
            $this->manager->flush();
            return new JsonResponse();
        }

}