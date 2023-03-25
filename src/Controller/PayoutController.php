<?php

namespace App\Controller;

use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\choixTemplate;
use App\Service\MangoPayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PayoutController extends AbstractController
{
    use RequestTrait,EntrepriseRepoTrait,SalarieRepoTrait;

    /**
     * @Route("/payout", name="payout")
     * @isGranted("ROLE_ENTREPRISE")
     * Montant total de la wallet MangoPay
     * @param MangoPayService $mangoPayService
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function index(MangoPayService $mangoPayService, choixTemplate $choixTemplate): Response
    {
        $user = $this->getUser();
        $wallet = $mangoPayService->oneWallet($user->getWalletMangoId());
        return $this->render('payout/index.html.twig', [

            'wallet' => $wallet
        ]);
    }

    /**
     * @Route("/effectuerRetrait")
     * @isGranted("ROLE_ENTREPRISE")
     * Retire de l'argent da la wallet MangoPay
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     */
    public function effectuerRetrait(MangoPayService $mangoPayService): JsonResponse
    {
        $montant = $this->request->getContent();

        $payOut = $mangoPayService->createPayOut($this->getUser(), (int)$montant);

        $response = new JsonResponse();
        if ($payOut->Status == 'CREATED') {
            $response->setData('Votre retrait a été enregistré');
        } else {
            $response->setData('Votre retrait a échoué');
        }
        return $response;
    }
}
