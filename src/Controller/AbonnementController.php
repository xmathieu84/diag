<?php

namespace App\Controller;

use App\Entity\EtatAbonnement;
use App\Form\MoisType;
use App\Helper\AboRepoTrait;
use App\Helper\AmbassadeurRepoTrait;
use App\Helper\CodePromoRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\EtatAbonnementRepository;
use App\Service\codeActivation;
use App\Service\DefinirDate;
use App\Service\choixTemplate;
use App\Service\Mail;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Mpdf\Mpdf;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbonnementController
 * @package App\Controller
 */
class AbonnementController extends AbstractController
{

    use AboRepoTrait, EntityManagerTrait, RequestTrait, etatAboRepoTrait,EntrepriseRepoTrait,SalarieRepoTrait,CodePromoRepoTrait,AmbassadeurRepoTrait;


    /**
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     * @Route("/abonnement", name="abonnement")
     * @isGranted("ROLE_ENTREPRISE")
     * @throws Exception
     */
    public function sAbonner(choixTemplate $choixTemplate, DefinirDate $definirDate): Response
    {

        if ($this->getUser()->hasRole('ROLE_ODI')){
            $abonnement = $this->abonnementsRepository->findBy(['cible'=>'odi']);
        }
        if ($this->getUser()->hasRole('ROLE_OTD')){
            $abonnement = $this->abonnementsRepository->findBy(['cible'=>'otd']);
        }
        $salarie = $this->salarieRepository->findOneBy(['user' => $this->getUser()]);
        $templateAE = $choixTemplate->templateAE($salarie->getEntreprise());
        $date = $definirDate->aujourdhui();
        $etat = $this->etatAbonnementRepository->trouverEtat($salarie->getEntreprise(), $date);

        return $this->render('entreprise/abonnement.html.twig', [
            'template' => $templateAE,
            'abonnements' => $abonnement,
            'entreprise' => $salarie->getEntreprise(),
            'etat' => $etat
        ]);
    }


    /**
     * @Route("/souscrire")
     * @isGranted("ROLE_ENTREPRISE")
     *
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws Exception
     */
    public function souscrireAbonnement(DefinirDate $definirDate): JsonResponse
    {
        $etat = new EtatAbonnement();
        $reponse = new JsonResponse();
        $contenu = json_decode($this->request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $entreprise = $this->getUser()->getSalarie()->getEntreprise();

        if ($contenu['code']){
            $code = $this->codePromoRepository->findForOtd($contenu['code'],$definirDate->aujourdhui());

                $abonnement = $code->getAbonnementOtd();
                $prix = $abonnement->getPrix() - $abonnement->getPrix()*$code->getRemise()/100;


        }
        else{
            $abonnement = $this->abonnementsRepository->findOneBy(['id' => $contenu['abonnement']]);
            $prix = $abonnement->getPrix();
        }
        if ($contenu['duree'] !=="" && $contenu['duree'] < 6 && $abonnement->getNom() === 'Infinite network') {
            return $reponse->setData([
                "message" => "Pour l'abonnement Infinite, la durée minimum est de 6 mois",
                'etat' => null,

            ]);
        } else {
            $dateDebut = $definirDate->aujourdhuiImmutable();
            $date = $definirDate->aujourdhuiImmutable();
            $jour = $dateDebut->format('d');
            $mois = $dateDebut->format('m');

            if ($jour ===29 || $jour === 30 || $jour === 31){
                ++$mois;
                $dateDebut = DateTimeImmutable::createFromFormat('d/m/Y','01/'.$mois.'/Y');
                $date = $dateDebut;

            }

            $etat->setDateDebut($dateDebut);
            if ($contenu['duree'] ===""){
                $dateFin = $date->add($abonnement->getDuree());
            }
            else{
                $dateFin = $date->add(new \DateInterval('P' . $contenu['duree'] . 'M'));
            }

            $entreprise->setCommission($abonnement->getCommission());
            $salarie = $this->salarieRepository->findOneBy(['user' => $this->getUser()]);
            $entreprise = $salarie->getEntreprise();
            $etat->setEntreprise($entreprise)
                ->setDatefin($dateFin)
                ->setAbonnement($abonnement)
                ->setMontant($prix)
                ->setAbonne(false);

           $this->manager->persist($entreprise);
            $this->manager->persist($etat);
            $this->manager->flush();

            return $reponse->setData(['etat' => $etat->getId(),  "message" => null]);
        }
    }

    /**
     * @param EtatAbonnementRepository $EtatRepository
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @Route("/recupereAbo")
     * @isGranted("ROLE_ENTREPRISE")
     * Recuperation de l'abonnement en cas de rechargement de page
     */
    public function retrouverAbonnememnt(EtatAbonnementRepository $EtatRepository, DefinirDate $definirDate)
    {
        $entreprise = $this->getUser()->getUserEnt();
        $id = $this->request->getContent();
        $abonnement = $this->abonnementsRepository->findOneBy(['id' => $id]);
        $date = $definirDate->aujourdhui();
        $etat = $EtatRepository->trouverEtatAbo($entreprise, $abonnement, $date);
        $reponse = new JsonResponse();
        if ($etat) {
            $cgv = $etat->getCgu();
            $dateDebut = $etat->getDateDebut();
            $dateFin = $etat->getDateFin();
            $interval = $dateFin->diff($dateDebut);
            $mois = $interval->format('%m');
            $reponse->setData(['cgv' => $cgv, 'duree' => $mois, 'id' => $etat->getId(), 'juridique' => $entreprise->getFormJuridique()]);
        }

        return $reponse;
    }

    /**
     * @param DefinirDate $definirDate
     * @return Response
     * @throws Exception
     * @Route ("/entreprise/changer abonnement",name="changerAbonnement")
     * @isGranted("ROLE_ENTREPRISE")
     */
    public function changeAbonnement(DefinirDate $definirDate){
        $salarie = $this->salarieRepository->findOneBy(['user' => $this->getUser()]);
        $date = $definirDate->aujourdhui();
        $entreprise = $salarie->getEntreprise();

        if(!$entreprise->getCgv()){
            $etat = $this->etatAbonnementRepository->trouverEtat($entreprise, $definirDate->aujourdhui());
            $entreprise->removeEtatAbonnement($etat);
            $this->manager->persist($entreprise);
            $this->manager->remove($etat);
            $this->manager->flush();
            return $this->redirectToRoute('abonnement');
        }

        $etat = $this->etatAbonnementRepository->findAbonnementEntreprise($entreprise);
        $abonnements = $this->abonnementsRepository->findForChange($etat->getAbonnement()->getPrix());
        $dureeR = $date->diff($etat->getDatefin());



        return $this->render('entreprise/changerAbonnement.html.twig',[
            'abonnements'=>$abonnements,
            'mois'=>$dureeR->format('%m')
        ]);

    }

    /**
     * @param $id
     * @param Mail $mail
     * @param DefinirDate $definirDate
     * @param codeActivation $codeActivation
     * @return JsonResponse
     * @throws Exception
     * @Route("/entreprise/validerChangementAbonnement/{id}")
     * @isGranted("ROLE_ENTREPRISE")
     */
    public function validerChangementAbonnement($id,Mail $mail,DefinirDate $definirDate,codeActivation $codeActivation){
        $abonnement = $this->abonnementsRepository->findOneBy(['id'=>$id]);
        $salarie = $this->salarieRepository->findOneBy(['user' => $this->getUser()]);
        $lien = $codeActivation->generer();
        $etat = new EtatAbonnement();
        $contenu = json_decode($this->request->getContent());
        $fin = $definirDate->duree($definirDate->aujourdhuiImmutable(),'P'.$contenu->duree.'M');
        if (count($salarie->getEntreprise()->getSalaries())>$abonnement->getOtdMax()){
            $montant = $abonnement->getOtdSup()*(count($salarie->getEntreprise()->getSalaries())-$abonnement->getOtdMax())+$abonnement->getPrix();
        }
        else{
            $montant = $abonnement->getPrix();
        }
        $entreprise = $salarie->getEntreprise();
        $etat->setEntreprise($entreprise)
            ->setAbonnement($abonnement)
            ->setAbonne(false)
            ->setMontant($montant)
            ->setCgu($contenu->conditionUtilisation)
            ->setDateDebut($definirDate->aujourdhuiImmutable())
            ->setDatefin($fin)
            ->setLien($lien);
        $entreprise->setCommission($abonnement->getCommission());
        $mail->mailChangementABonnement($salarie->getUser()->getEmail(),$montant,$abonnement,$lien);
        $this->manager->persist($entreprise);
        $this->manager->persist($etat);
        $this->manager->flush();
        return new JsonResponse('ok');


    }

    /**
     * @param string $lien
     * @param DefinirDate $definirDate
     * @return Response
     * @throws NonUniqueResultException
     * @Route("/entreprise/valider nouvel abonnement/{lien}")
     * @isGranted("ROLE_ENTREPRISE")
     */
    public function accepterAbonnement(string $lien,DefinirDate $definirDate){
        $etat = $this->etatAbonnementRepository->findOneBy(['lien'=>$lien]);
        $user = $this->getUser();
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $oldEtat = $this->etatAbonnementRepository->findAbonnementEntreprise($salarie->getEntreprise());
        $salaries = $this->salarieRepository->findSalarie($salarie->getEntreprise());
        switch ($etat->getAbonnement()->getNom()){
            case 'Classic access':
                $role = ['ROLE_ENTREPRISE',"ROLE_CLASSIC"];
                $roleS = ['ROLE_SALARIE','ROLE_CLASSIC'];
                break;

            case 'Premium network':
                $role = ['ROLE_ENTREPRISE',"ROLE_PREMIUM"];
                $roleS = ['ROLE_SALARIE','ROLE_PREMIUM'];
                break;

            case 'Infinite newtork':

                $role = ['ROLE_ENTREPRISE',"ROLE_PREMIUM"];
                $roleS = ['ROLE_SALARIE','ROLE_PREMIUM'];

        }
        $salarie->getUser()->setRoles($role);
        $this->manager->persist($salarie);
        foreach ($salaries as $salary){
            $salary->getUser()->setRoles($roleS);
            $this->manager->persist($salary);
        }
        $etat->setAbonne(true);
        $oldEtat->setAbonne(true)
        ->setLien(null);
        $this->manager->persist($etat);
        $this->manager->persist($oldEtat);
        $this->manager->flush();


        return $this->render('entreprise/validerNewAbonnement.html.twig');

    }

    /**
     * @Route("/test")
     */
    public function testSpider():Response{
        $test = file_get_contents("https://127.0.0.1:8000/spider.xml");
        dump($test);
        return $this->render('test.html.twig');
    }
}
