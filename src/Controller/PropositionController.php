<?php

namespace App\Controller;

use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Service\choixTemplate;
use App\Service\FactureCommisionInter;
use App\Service\Mail;
use App\Service\PropChoix;
use DateTime;
use App\Service\Geoloc;
use App\Helper\RequestTrait;
use App\Helper\InterRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\PropositionRepoTrait;
use App\Helper\ReservationRepoTrait;
use App\Helper\TauxHoraireRepoTrait;
use App\Repository\PourcentageRepository;
use App\Helper\ListeInterTypeInterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\DefinirObjet;
use DateTimeImmutable;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PropositionController extends AbstractController
{
    use InterRepoTrait,
        EntityManagerTrait,
        RequestTrait,
        TauxHoraireRepoTrait,
        ListeInterTypeInterRepoTrait,
        PropositionRepoTrait,
        SalarieRepoTrait,
        InterRepoTrait,
        ReservationRepoTrait,EntrepriseRepoTrait,
        DemandeurRepoTrait,AgentRepoTrait;

    /**
     * @Route("/proposition/{id}/{code}", name="proposition")
     * @Security("is_granted('ROLE_RESPONSABLE') and is_granted('ROLE_ABONNE') or is_granted('ROLE_DEMANDEUR')")
     * @param $id
     * @param choixTemplate $choixTemplate
     * @param string|null $code
     * @return Response
     */
    public function voirProp($id,choixTemplate $choixTemplate,PourcentageRepository $repository,string $code=null,): Response
    {
        $user = $this->getUser();
        if ($user->hasRole('ROLE_DEMANDEUR')){
            $demandeur = $this->getUser()->getDemandeur();
        }
        elseif ($user->hasRole('ROLE_SYNDIC')){
                $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
                $demandeur = $agent->getDemandeur();

        }
        else{
            $demandeur = $this->getUser()->getAgent()->getDemandeur();
        }

        $propositionsPrix = $this->propositionRepository->propositionAvecPrix($id,$demandeur);
        $propositionsSansPrix = $this->propositionRepository->propositionSansPrix($id,$demandeur);
        $template = $choixTemplate->templateDem($this->getUser());
        $user = $this->getUser();
        $tva = $repository->findOneBy(['nom'=>'tva']);
        return $this->render('proposition/vueProposition.html.twig', [
            'propositionsPrix' => $propositionsPrix,
            'propositionsSansPrix'=>$propositionsSansPrix,
            'user' => $user,
            'template'=>$template[0],
            'code'=>$code,
            'tva'=>$tva
        ]);
    }

    /**
     * @Route("/propositionTaux")
     * @Security("is_granted('ROLE_RESPONSABLE') and is_granted('ROLE_ABONNE') or is_granted('ROLE_DEMANDEUR')")
     * @return JsonResponse
     */
    public function propTaux(): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $LITI = $this->listeInterTypeInterRepository->findOneBy(['listeInter' => $contenu['liste'], 'typeInter' => $contenu['type']]);
        $tauxHoraire = $this->tauxHoraireRepository->findOneBy(['inter' => $LITI, 'salarie' => $contenu['salarie']]);
        $taux = $tauxHoraire->getTaux();
        $coutMin = $tauxHoraire->getPrixMinimum();
        $reponse = new JsonResponse();
        return $reponse->setData([
            'taux' => $taux,
            'prixMin' => $coutMin
        ]);
    }

    /**
     * @Route("/propKm")
     * @Security("is_granted('ROLE_RESPONSABLE') and is_granted('ROLE_ABONNE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC') and is_granted('ROLE_ABONNE')")
     * @param Geoloc $geoloc
     * @param DefinirObjet $definirObjet
     * @return JsonResponse
     */
    public function propKm(Geoloc $geoloc, DefinirObjet $definirObjet): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $intervention = $this->interventionRepository->findOneBy(['id' => $contenu['intervention']]);
        $coordInter = $definirObjet->obtenirCordonnee($intervention);
        $salarie = $this->salarieRepository->findOneBy(['id' => $contenu['salarie']]);
        $coordSalarie = $definirObjet->obtenirCordonnee($salarie);
        $distance = $geoloc->coutKilometre($coordInter[0], $coordInter[1], $coordSalarie[0], $coordSalarie[1]);
        $reponse = new JsonResponse();

        return $reponse->setData($distance);
    }

    /**
     * @Route("/accordProposition/{id}/{code}",name="accordProposition")
     * @Security("is_granted('ROLE_RESPONSABLE') and is_granted('ROLE_ABONNE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC') and is_granted('ROLE_ABONNE')")
     * @param int $id
     * @param Geoloc $geoloc
     * @param Mail $mail
     * @param PourcentageRepository $pourcentageRepository
     *
     * @throws TransportExceptionInterface
     */
    public function accordProposition(int $id, Geoloc $geoloc, Mail $mail, PourcentageRepository $pourcentageRepository,FactureCommisionInter $factureCommisionInter,PropChoix $propChoix,string $code=null,)
    {
        $proposition = $this->propositionRepository->findOneBy(['id' => $id]);

        $intervention = $proposition->getInter();
        $user = $this->getUser();
        $Taux = $pourcentageRepository->findOneBy(['nom'=>'acompte']);
        $taux = $Taux->getTaux() / 100;

        $intervention->setPrix($proposition->getPrix() + $proposition->getIndemnite())
            ->setRdvAT($proposition->getDatePropose())
            ->setPropositionChoisie($proposition);
        if ($user->hasRole('ROLE_INSTITUTION') || $user->hasRole('ROLE_GRANDCOMPTE')) {
            $intervention->setStatuInter('Intervention validée');
            $intervention->setAcommpte(0);
            $propChoix->traitementProposition($intervention,$proposition);

        } else {
            $acompte= $factureCommisionInter->factureAccompte($intervention);
            $intervention->setStatuInter('En attente de paiement')
                    ->setAcommpte(($proposition->getPrix() + $proposition->getIndemnite()) * $taux)
                    ->setDevis($acompte);

        }
        $facture = $factureCommisionInter->factureIntervention($intervention,$user);
        $intervention->setFacture($facture);
        $this->manager->persist($intervention);
        $this->manager->flush();
        if ($user->hasRole('ROLE_INSTITUTION') || $user->hasRole('ROLE_GRANDCOMPTE')) {
            return $this->redirectToRoute('demandeur_encours',['code'=>$code]);

        } else {
            return $this->redirectToRoute('paiement',['type'=>'acompte','id'=>$intervention->getId()]);

        }


    }

    /**
     *
     * @return Response
     * @Route ("/historiqueProposition",name="histoProp")
     * @isGranted ("ROLE_SALARIE")
     * 
     */
    public function historiquePrposition():Response
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
         $entreprise = $salarie->getEntreprise();

        $propositions = $this->propositionRepository->findByEntreprise($entreprise);
        return $this->render('proposition/historiqueProposition.html.twig',[

            'propositions'=>$propositions
        ]);

    }
}
