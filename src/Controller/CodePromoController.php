<?php

namespace App\Controller;

use App\Entity\EtatAbonnement;

use App\Helper\AmbassadeurRepoTrait;
use App\Helper\CodePromoRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\MailPrefectureRepository;
use App\Service\DefinirDate;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class CodePromoController extends AbstractController
{
    use RequestTrait,CodePromoRepoTrait,AmbassadeurRepoTrait,DemandeurRepoTrait,EntityManagerTrait,EntrepriseRepoTrait;

    /**
     * @return JsonResponse
     * @Route("/verifcation/codePromo")
     */
    public function codePromoGc(MailPrefectureRepository $repository):JsonResponse{
        $content = json_decode($this->request->getContent());
        $code = $this->codePromoRepository->findOneBy(['profil'=>$content->profil,"codeReduc"=>$content->code]);
        $ambassadeur = $this->ambassadeurRepository->findOneBy(['profil'=>$content->profil,"codeReduc"=>$content->code]);
        $response = new JsonResponse();
        if ($code){
            $response->setData([
                'abonnement'=>$code->getAbonnementGci()->getNom(),
                'remise'=>$code->getRemise(),
                'existe'=>'promo',
                'utilisateur'=>$code->getAbonnementGci()->getUtlisateur(),
                'prix'=>$code->getAbonnementGci()->getPrix()*$code->getRemise()/100
            ]);
        }elseif ($ambassadeur){
            $cp = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$content->cp));
            $departement = $repository->findOneBy(['numeroDepartement'=>$cp[0]->codeDepartement]);
            $grandCompte = $this->demandeurRepository->findAmbassadeurGc($departement,$ambassadeur);

           if (count($grandCompte)<$ambassadeur->getMaximum()){
               $response->setData([
                   'abonnement'=>$ambassadeur->getAbonnementGci()->getNom(),
                   'possible'=>true,
                   'existe'=>'ambassadeur',
                   'utilisateur'=>$ambassadeur->getAbonnementGci()->getUtlisateur(),
                   'prix'=>$ambassadeur->getPrix(),
                   'duree'=>$ambassadeur->getDureeAbo()->format("%m"),
                   'commentaire'=>$ambassadeur->getCommentaire()]);
           } else{
               $response->setData([
                   'existe'=>'ambassadeur',
                   'possible'=>false
               ]);
           }

        }
        else{
            $response->setData([
                'existe'=>false
            ]);
        }

        return $response;
    }

    /**
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/verification/codePromoInsti")
     */
    public function codePromoInsti(DefinirDate $definirDate,MailPrefectureRepository $repository):JsonResponse{
        $content = json_decode($this->request->getContent());
        $code = $this->codePromoRepository->codePromoByHabitant($content->habitant,$content->code,$definirDate->aujourdhui());
        $ambassadeur = $this->ambassadeurRepository->ambassadeurByHabitant($content->habitant,$content->code);
        $response = new JsonResponse();
        if ($code){
            $response->setData([
                'abonnement'=>$code->getAbonnementGci()->getNom(),
                'remise'=>$code->getRemise(),
                'existe'=>true,
                'utilisateur'=>$code->getAbonnementGci()->getUtlisateur(),
                'prix'=>$code->getAbonnementGci()->getPrix()*$code->getRemise()/100
            ]);
        }
        elseif ($ambassadeur){
            $cp = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$content->cp));
            $departement = $repository->findOneBy(['numeroDepartement'=>$cp[0]->codeDepartement]);
            $institution = $this->demandeurRepository->findAmbassadeurInsti($departement,$ambassadeur);
            if (count($institution)<$ambassadeur->getMaximum()){
                $response->setData([
                    'abonnement'=>$ambassadeur->getAbonnementGci()->getNom(),
                    'possible'=>true,
                    'existe'=>'ambassadeur',
                    'utilisateur'=>$ambassadeur->getAbonnementGci()->getUtlisateur(),
                    'prix'=>$ambassadeur->getPrix(),
                    'duree'=>$ambassadeur->getDureeAbo()->format("%m"),
                    'commentaire'=>$ambassadeur->getCommentaire()]);
            } else{
                $response->setData([
                    'existe'=>'ambassadeur',
                    'possible'=>false
                ]);
            }
        }
        else{
            $response->setData([
                'existe'=>false
            ]);
        }

        return $response;
    }

    /**
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/verfication/promoOtd")
     */
    public function codePromoOtd(DefinirDate $definirDate):JsonResponse{
        $code = $this->codePromoRepository->findForOtd($this->request->getContent(),$definirDate->aujourdhui());
        $ambassadeur = $this->ambassadeurRepository->findOneBy(['codeReduc'=>$this->request->getContent()]);
        $reponse = new JsonResponse();
        if ($code){
            $reponse->setData([

                'id'=>$code->getAbonnementOtd()->getId(),
                'nom'=>$code->getAbonnementOtd()->getNom(),
                'remise'=>$code->getRemise(),
                'existe'=>'promo',
                'prix'=>number_format($code->getAbonnementOtd()->getPrix() - $code->getAbonnementOtd()->getPrix()*$code->getRemise()/100,2),
                'ht'=>number_format(1.2*$code->getAbonnementOtd()->getPrix() - $code->getAbonnementOtd()->getPrix()*$code->getRemise()/100)

            ]);

        }
        elseif ($ambassadeur){
            $listeEnt = $this->entrepriseRepository->findAmbassadeur($this->request->getContent(),$this->getUser()->getSalarie()->getEntreprise()->getAdresse()->getDepartement());
            if (count($listeEnt)< $ambassadeur->getMaximum()){
                $reponse->setData([
                    'abonnement'=>$ambassadeur->getAbonnementOtd()->getNom(),
                    'existe'=>'ambassadeur',
                    'prix'=>$ambassadeur->getPrix(),
                    'duree'=>$ambassadeur->getDureeAbo()->format('%m'),
                    'possible'=>true,
                    'code'=>$this->request->getContent()
                ]);

            }
            else{
                $reponse->setData([
                    'existe'=>'ambassadeur',
                    'possible'=>false
                ]);
            }

        }
        else{
            $reponse->setData(['existe'=>false]);
        }
        return $reponse;
    }

    /**
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws \Exception
     * @Route("/valider/amabassadeur")
     */
    public function validerAmbassadeur(DefinirDate $definirDate){
        $ambassadeur = $this->ambassadeurRepository->findOneBy(['codeReduc'=>$this->request->getContent()]);
        $dateDebut = $definirDate->aujourdhuiImmutable();
        $date = $definirDate->aujourdhuiImmutable();
        $jour = $dateDebut->format('d');
        $mois = $dateDebut->format('m');

        if ($jour ===29 || $jour === 30 || $jour === 31){
            ++$mois;
            $dateDebut = DateTimeImmutable::createFromFormat('d/m/Y','01/'.$mois.'/Y');
            $date = $dateDebut;

        }
        else{
            $date = $definirDate->aujourdhuiImmutable();
        }
        $entreprise = $this->getUser()->getSalarie()->getEntreprise();
        $etat = new EtatAbonnement();
        $etat->setDateDebut($date)
            ->setAbonnement($ambassadeur->getAbonnementOtd())
            ->setDatefin($date->add($ambassadeur->getDureeAbo()))
            ->setEntreprise($this->getUser()->getSalarie()->getEntreprise())
            ->setMontant($ambassadeur->getPrix())
            ->setReconduction(false)
            ->setAbonne(false);
        $entreprise->setCommission($ambassadeur->getAbonnementOtd()->getCommission());
        $this->manager->persist($entreprise);
        $this->manager->persist($etat);
        $this->manager->flush();
        return new JsonResponse(["reponse"=>'ok']);


    }
}