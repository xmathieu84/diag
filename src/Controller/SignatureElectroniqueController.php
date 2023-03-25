<?php

namespace App\Controller;

use App\Entity\SepaSigne;
use App\Helper\AgentRepoTrait;
use App\Helper\BanqueRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\BanqueRepository;
use App\Repository\MandatCerfaRepository;
use App\Repository\UserRepository;
use App\Service\DefinirDate;
use App\Service\FacturePdfService;
use App\Service\Fichier;
use App\Service\Yousign;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WiziYousignClient\WiziSignClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SignatureElectroniqueController extends AbstractController
{
    use EntrepriseRepoTrait, InterRepoTrait,SalarieRepoTrait,etatAboRepoTrait,AgentRepoTrait,RequestTrait,EntityManagerTrait,BanqueRepoTrait;


    /**
     * @Route("/signature/electronique", name="signature_electronique")
     * @Security("is_granted('ROLE_GRANDCOMPTE') and is_granted('ROLE_MANITOU') or is_granted('ROLE_ENTREPRISE') or is_granted('ROLE_BTP')")
     */
    public function index(DefinirDate $definirDate,Yousign $yousign,Fichier $fichier,BanqueRepository $banqueRepository,UserRepository $userRepository)
    {

        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);

        if ($agent){
            $debiteur = $agent->getDemandeur();
            $nom = $agent->getCivilite()->getNom();
            $prenom = $agent->getCivilite()->getPrenom();
            $tel = $agent->getDemandeur()->getTelephon()->getNumero();
            $agent->setCgv(true);
            $this->manager->persist($agent);
            $this->manager->flush();
            $retour = 'homeInsti';
            $banque = $banqueRepository->findOneBy(['institution'=>$agent->getDemandeur(),'actif'=>true]);

        }else{
            $debiteur = $salarie->getEntreprise();
            $nom = $salarie->getCivilite()->getNom();
            $prenom = $salarie->getCivilite()->getPrenom();
            $tel = $salarie->getTelephone()->getNumero();
            $retour = 'enAttenteDuMandat';
            $banque = $banqueRepository->findOneBy(['entreprise'=>$salarie->getEntreprise(),'actif'=>true]);

        }
        $dossier = $this->getParameter('sepa_directory');


        $file = $yousign->createFile($dossier.'/'.$banque->getSepa(),$banque->getSepa());

       $yousign->notifEmailSepa($this->getUser()->getId(),$nom,$prenom,$this->getUser()->getEmail(),$tel,$file['id']);

        return $this->redirectToRoute($retour);





    }

    /**
     * @Route("/signerMandatCerfa/{entreprise}/{intervention}",name="signerMandatCerfa")
     * @Security("is_granted('ROLE_SALARIE')")
     *
     * @param [type] $entreprise
     * @param [type] $intervention
     * @param MandatCerfaRepository $mandatCerfaRepository
     * @return RedirectResponse
     */
    public function signerMandatCerfa($entreprise, $intervention, MandatCerfaRepository $mandatCerfaRepository,Yousign $yousign)
    {

        $societe = $this->entrepriseRepository->findOneBy(['id' => $entreprise]);
        $inter  = $this->interventionRepository->findOneBy(['id' => $intervention]);
        $salarie = $this->getUser()->getSalarie();

        $mandat = $inter->getMandatCerfa();
        $dossier = $this->getParameter('mandatCerfa_directory');
        $file = $yousign->createFile($dossier.'/'.$mandat->getFichierMandat(),$mandat->getFichierMandat());
        $yousign->notifEmailMandatCerfa($mandat->getId(),$salarie->getCivilite()->getNom(),$salarie->getCivilite()->getPrenom(),$this->getUser()->getEmail(),$salarie->getTelephone()->getNumero(),$file['id']);


        return $this->redirectToRoute('mesinter');


    }




}


