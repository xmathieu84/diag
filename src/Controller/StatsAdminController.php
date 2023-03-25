<?php


namespace App\Controller;


use App\Helper\AboRepoTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsAdminController extends AbstractController
{
    use AboRepoTrait;

    /**
     * @return Response
     * @Route("/administrateur/stats",name="stats")
     */
    public function stats()
    {
        $abonnement = $this->abonnementsRepository->findAll();
        return $this->render('administrateur/stats.html.twig', [
            'abonnements' => $abonnement
        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/administrateur/stats/abonnements")
     */
    public function statAbonnement(): JsonResponse
    {
        $statAbonnements = [];
        $abonnements = $this->abonnementsRepository->findBy(['cible' => 'otd']);
        $abonnnementIs = $this->abonnementsRepository->findBy(['cible' => 'gci']);
        foreach ($abonnements as $abonnement) {

            array_push($statAbonnements, ['nom' => $abonnement->getNom(), 'nombre' => sizeof($abonnement->getEtatAbonnements())]);

        }
        foreach ($abonnnementIs as $abonnnementI) {
            array_push($statAbonnements, ['nom' => $abonnnementI->getNom(), 'nombre' => sizeof($abonnnementI->getAboTotalInstis())]);
        }

        return new JsonResponse($statAbonnements);
    }
}