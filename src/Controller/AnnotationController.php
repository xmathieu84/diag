<?php


namespace App\Controller;


use App\Helper\AnnonationRepoTrait;
use App\Helper\DocSouDossierRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AnnotationController extends AbstractController
{
    use AnnonationRepoTrait,RequestTrait,EntityManagerTrait,DocSouDossierRepoTrait;

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/annotation/{id}")
     */
    public function ancienneAnnotation(int $id){
        $annotations = $this->annotationRepository->findByDoc($id);
        $reponse =[];
        foreach ($annotations as $annotation){
            $reponse[] = [
                'titre' => $annotation->getTitre(),
                'texte' => $annotation->getTexte(),
                'date' => $annotation->getDate()->format('d/m/Y'),
                'auteur' => $annotation->getAuteur(),
                'id' => $annotation->getId()
            ];
        }
        return new JsonResponse($reponse);
    }

    /**
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws Exception
     * @Route("/supprimerNoteFichier")
     */
    public function supprimerNoteFichier(DefinirDate $definirDate){
        $note = $this->annotationRepository->findOneBy(['id'=>$this->request->getContent()]);

        $agent = $this->getUser()->getAgent();
       $dossier = $note->getDocSousDossier()->getSousDossier()->getDossier();
        $dossier->setTypeModif('Suppression de la note du fichier '.$note->getDocSousDossier()->getLibelle())
            ->setDateModif($definirDate->aujourdhui())
            ->setNomModifiant($agent->getCivilite()->getPrenom().' '.$agent->getCivilite()->getNom());

       $this->manager->remove($note);
        $this->manager->persist($dossier);
        $this->manager->flush();

        return new JsonResponse();


    }
    public function deleteFileDossier(){

    }


}