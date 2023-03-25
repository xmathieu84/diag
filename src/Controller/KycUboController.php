<?php

namespace App\Controller;

use App\Entity\KycDeclaration;
use App\Entity\UboDeclaration;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\DefinirDate;
use App\Service\MangoPayService;
use DateTime;
use MangoPay\Libraries\Exception;
use MangoPay\Libraries\ResponseException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class KycUboController extends AbstractController
{
    use EntrepriseRepoTrait,SalarieRepoTrait,etatAboRepoTrait,EntityManagerTrait;
    /**
     * @Route("/kyc/ubo", name="kyc_ubo")
     * @isGranted("ROLE_ENTREPRISE")
     */
    public function index(DefinirDate $definirDate): Response
    {
        $entreprise = $this->getUser()->getSalarie()->getEntreprise();

        $etat = $this->etatAbonnementRepository->trouverEtat($entreprise,$definirDate->aujourdhui());

        if($entreprise->getUboDeclaration()){
            if(!$entreprise->getCgv()) return $this->redirectToRoute("terminerInscription");
            else $this->redirectToRoute('entreprise');
        }

        return $this->render('entreprise/kyc_ubo.html.twig',[
            'abonnement'=>$etat->getAbonnement(),
            'entreprise'=>$entreprise
        ]);
    }

    /**
     * @Route("/loadKyc")
     * @isGranted("ROLE_ENTREPRISE")
     * @param Request $request
     * @param MangoPayService $mangoPayService
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws Exception
     * @throws ResponseException
     */
    public function loadKycUbo(Request $request, MangoPayService $mangoPayService,DefinirDate $definirDate)
    {

        $user = $this->getUser();
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $adresse =$entreprise->getAdresse();
        $identite = $request->files->get('identite');
        $reponse = new JsonResponse();

        $ident = $mangoPayService->createKYC($user->getMangoPayId(), $identite, 'IDENTITY_PROOF');
        $kycIdent = new KycDeclaration();
        $kycIdent->setType('Identite')
            ->setDateEnvoi($definirDate->aujourdhui())
            ->setEntreprise($entreprise)
            ->setIdKyc($ident->Id)
            ->setReponse('demandée');
        $this->manager->persist($kycIdent);

            

        $kbis = $request->files->get('kbis');
        $Kbis = $mangoPayService->createKYC($user->getMangoPayId(), $kbis, 'REGISTRATION_PROOF');
        $kycKbis = new KycDeclaration();
        $kycKbis->setType('kbis')
            ->setDateEnvoi($definirDate->aujourdhui())
            ->setEntreprise($entreprise)
            ->setIdKyc($Kbis->Id)
            ->setReponse('demandée');
        $this->manager->persist($kycKbis);



        $statut = $request->files->get('statut');
        if ($statut) {
            $Statut = $mangoPayService->createKYC($user->getMangoPayId(), $statut, 'ARTICLES_OF_ASSOCIATION');
            $kycStatut = new KycDeclaration();
            $kycStatut->setType('statut')
                ->setDateEnvoi($definirDate->aujourdhui())
                ->setEntreprise($entreprise)
                ->setIdKyc($Statut->Id)
                ->setReponse('demandée');
            $this->manager->persist($kycStatut);
        }
        if ($salarie->getEntreprise()->getFormJuridique()!=="auto-entrepreneur"){
            $dateNaissance = $request->request->get('naissance');
            $date = DateTime::createFromFormat('Y-m-d', $dateNaissance);
            $lieuNaissance = $request->request->get('lieu');

            $ubo = $mangoPayService->createUBO($user->getMangoPayId(), $entreprise, $adresse, $date, $lieuNaissance);

            $uboDecla = new UboDeclaration();
            $uboDecla->setResultat('demandée')
                ->setDateDemande($definirDate->aujourdhui())
                ->setIdUbo($ubo->Id)
                ->setEntreprise($entreprise);
            $this->manager->persist($uboDecla);
            $this->manager->flush();


            if ($ubo->Status === 'VALIDATION_ASKED' && $Kbis == true && $ident ==true) {
                $reponse->setData(['reponse'=>'ok']);
            } else {
                $reponse->setdata(['reponse'=>'Un élément est manquant. Veuillez nous contacter pour résoudre le problème.Vous allez être redirigé dans quelque seconde pour continuer votre inscription']);
            }
        }
        else{
            $reponse->setData(['reponse'=>'ok']);
        }



        return $reponse;
    }
}
