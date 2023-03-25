<?php

namespace App\Controller;

use App\Entity\MangoPayOut;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Repository\MangoPayInRepository;
use App\Repository\UserRepository;
use App\Service\DefinirDate;
use App\Service\MangoPayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPaiementController extends AbstractController
{
    use EntityManagerTrait, InterRepoTrait;

    /**
     * @Route("/admin/interEnAttente",name="interEnAttente")
     *
     * @return Response
     */
    public function interEnAttente(MangoPayInRepository $mangoPayInRepo): Response
    {
        $interventions = $this->interventionRepository->findBy(['statuInter' => 'en attente de virement']);
        $mangoPayIn = $mangoPayInRepo->findByInterEnAttente();

        return $this->render('admin_paiement/interEnattente.html.twig', [
            'mangos' => $mangoPayIn
        ]);
    }

    /**
     * @Route("/admin/validerInter-{id}",name="validerInter")
     *
     * @param [type] $id
     * @param MangoPayInRepository $mangoPayInRepository
     * @return RedirectResponse
     */
    public function validerInter($id, MangoPayInRepository $mangoPayInRepository): RedirectResponse
    {
        $mango = $mangoPayInRepository->findOneBy(['id' => $id]);
        $intervention = $mango->getIntervention();
        if ($intervention->getIntRap()) {
            $intervention->setStatuInter('termine');
        } else {
            $intervention->setStatuInter('Intervention validée');
        }
        $this->manager->persist($intervention);
        $this->manager->flush();
        return $this->redirectToRoute('interEnAttente');
    }
}
