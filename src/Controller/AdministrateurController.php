<?php

namespace App\Controller;

use App\Entity\CoorOtd;
use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\InterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Helper\UserRepoTrait;
use App\Repository\FichierOTDRepository;
use App\Service\DefinirDate;
use DateTime;


use Exception;
use SMSFactor\Message;

use SMSFactor\SMSFactor;
use App\Entity\TarifAdmin;
use App\Form\PourcentType;
use App\Entity\Pourcentage;
use App\Form\TarifAdminType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use App\Entity\SecteurIntervention;
use App\Form\ModifierTarifAdminType;
use App\Form\SecteurInterventionType;
use App\Repository\EntrepriseRepository;
use App\Repository\TarifAdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AbonnementsRepository;
use App\Repository\PourcentageRepository;
use App\Repository\InterventionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdministrateurController extends AbstractController
{
    use UserRepoTrait,InterRepoTrait,SalarieRepoTrait,AgentRepoTrait,DemandeurRepoTrait;
    /**
     * @Route("/administrateur", name="administrateur")
     */
    public function index()
    {
        $entrepriseOtd = $this->userRepository->findForStatAdmin("ROLE_ENTREPRISE");
        $demandeur = $this->userRepository->findForStatAdmin('ROLE_DEMANDEUR');
        $grandC = $this->userRepository->findForStatAdminGc('ROLE_GRANDCOMPTE','ROLE_MANITOU');
        $insti = $this->userRepository->findForStatAdminGc('ROLE_INSTITUTION','ROLE_MANITOU');
        $agentGc = $this->userRepository->findForStatAdmin('ROLE_GRANDCOMPTE');
        $agentI = $this->userRepository->findForStatAdmin('ROLE_INSTITUTION');
        $otd = $this->userRepository->findForStatAdmin('ROLE_SALARIE');
        $interventionTermine = $this->interventionRepository->findBy(['statuInter'=>'termine']);
        $interventionDemande = $this->interventionRepository->findBy(['statuInter'=>'Nouvelle demande']);
        $interventionValide = $this->interventionRepository->findBy(['statuInter'=>'intervention validée']);
        $salaries = $this->salarieRepository->findSalarieConnecte();
        $collaborateurs = $this->agentRepository->findConnecte('ROLE_GRANDCOMPTE');
        $agents = $this->agentRepository->findConnecte('ROLE_INSTITUTION');
        $demandeurs = $this->demandeurRepository->findConnect();
        return $this->render('administrateur/index.html.twig', [
            'entrepriseOtd'=>count($entrepriseOtd),
            'demandeur'=>count($demandeur),
            'grandC'=>count($grandC),
            'intsi'=>count($insti),
            'agentGc'=>count($agentGc),
            'agentI'=>count($agentI),
            'otd'=>count($otd),
            'interDemande'=>count($interventionDemande),
            'interValide'=>count($interventionValide),
            'intertermine'=>count($interventionTermine),
            "salaries"=>$salaries,
            'collaborateurs'=>$collaborateurs,
            'agents'=>$agents,
            'demandeurs'=>$demandeurs

        ]);
    }


    /**
     * @Route("/administrateur/taux",name="taux")
     * @Route("/administrateur/taux/{id}",name="modifierTaux")
     */
    public function dronediag(
        EntityManagerInterface $manager,
        Request $request,
        PourcentageRepository $pourRepo,
        $id = null
    ) {
        $taux = $pourRepo->findAll();

        if ($id == null) {
            $pourcent = new Pourcentage();
        } else {
            $pourcent = $pourRepo->findOneBy(['id' => $id]);
        }

        $form = $this->createForm(PourcentType::class, $pourcent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($pourcent);
            $manager->flush();
            return $this->redirectToRoute('taux');
        }
        return $this->render('administrateur/pourcentage.html.twig', [
            'form' => $form->createView(),
            'tauxs' => $taux
        ]);
    }

    /**
     * @Route("/administrateur/supprimer/{id}",name="supprimerTaux")
     * @param EntityManagerInterface $manager
     * @param PourcentageRepository $pourRepo
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function supprimerTaux(
        EntityManagerInterface $manager,
        PourcentageRepository $pourRepo,
        $id = null
    ) {

        $taux = $pourRepo->findOneBy(['id' => $id]);
        $manager->remove($taux);
        $manager->flush();
        return $this->redirectToRoute('taux');
    }

    /**
     * @param DefinirDate $definirDate
     * @param PourcentageRepository $pourcentageRepository
     * @param EntrepriseRepository $entrepriseRepository
     * @return Response
     * @throws Exception
     * @Route("/administrateur/prelevementSup",name="prelevementSup")
     */
    public function relevementSup(DefinirDate $definirDate,PourcentageRepository $pourcentageRepository,EntrepriseRepository $entrepriseRepository):Response{
        $dateDebut = $definirDate->debutDuMois();
        $dateFin = $definirDate->finDuMois();
        $entreprises = $entrepriseRepository->findByMandatCerfa($dateDebut,$dateFin);

        return $this->render('administrateur/bilan.html.twig');
    }



}

