<?php

namespace App\Controller;

use App\Entity\ComuComMailing;
use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\ComuComMailingRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Geoloc;
use App\Service\InterRegionDep;
use App\Service\listeInterDepartementRegion;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class InstitutionnelController extends AbstractController
{
    use InterRepoTrait, RequestTrait,DemandeurRepoTrait,AgentRepoTrait,EntityManagerTrait;
    /**
     * @Route("/les interventions proches/{code}", name="instituListeInter")
     *
     * @Security("is_granted('ROLE_NIVEAU1') and is_granted('ROLE_ABONNE')")
     */
    public function listeInter(choixTemplate $choixTemplate,ComuComMailingRepository $repository,string $code=null): Response
    {

        $user = $this->getUser();
        $template = $choixTemplate->templateDem($user);

        return $this->render('institutionnel/listeInter.html.twig', [
            'user' => $user,
            'template' => $template[0],
            'code'=>$code

        ]);
    }

    /**
     * @Route("/institutionnel/recuperer/listeInter")
     * @Security("is_granted('ROLE_NIVEAU1') and is_granted('ROLE_ABONNE') or is_granted('ROLE_NIVEAU1GC') and is_granted('ROLE_ABONNE')")
     * @param InterRegionDep $interRegionDep
     * @return JsonResponse
     */
    public function listeInterDate(InterRegionDep $interRegionDep,DefinirDate $definirDate): JsonResponse
    {
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $content = $this->request->getContent();
        $date = $definirDate->duree($definirDate->aujourdhui(),$content);

        $demandeur = $agent->getDemandeur();
        $interventions = $interRegionDep->interventionProche($demandeur,$date);
        $liste = [];
        foreach ($interventions as $intervention) {

            $otdFree = $interRegionDep->dispoOTD($intervention);

            $liste[] = [
                'inter' => [
                    $intervention->getRdvAt()->format('d/m/Y H:i'),
                    $intervention->getAdresse()->getCodePostal() . ' ' . $intervention->getAdresse()->getVille(),
                    $intervention->getListeInter()->getNom() . ' ' . $intervention->getTypeInter()->getNom(),

                ],
                'result' => $otdFree,
                'tipi' => [
                    'typeInter' => $intervention->getTypeInter()->getId(),
                    'listeInter' => $intervention->getListeInter()->getId(),
                    'codeP' => $intervention->getAdresse()->getCodePostal(),
                    'ville' => $intervention->getAdresse()->getVille(),
                    'otd' => $intervention->getReservation()->getSalarie()->getId(),
                    'date' => $intervention->getRdvAt()->getTimestamp()
                ]
            ];
        }
        return new JsonResponse($liste);

    }
    /**
     * @return Response
     * @Route ("/recherche par ville/{code}",name="parVille")
     */
    public function listeInterVilleChoix(choixTemplate $choixTemplate,string $code =null)
    {
        $template = $choixTemplate->templateDem($this->getUser());
        return $this->render('institution/listeParVille.html.twig',['template'=>$template[0],'code'=>$code]);
    }

    /**
     * @param Geoloc $geoloc
     * @param DefinirDate $definirDate
     * @param InterRegionDep $interRegionDep
     * @return JsonResponse
     * @throws Exception
     * @Route ("/institution/listeInter/ville")
     */
    public function listeInterVille(Geoloc $geoloc, DefinirDate $definirDate, InterRegionDep $interRegionDep)
    {
        $content = json_decode($this->request->getContent(), true);
        $liste = [];
        $coordonneeDepart = $geoloc->localise($content['lieu']);
        $rayonRecherche = $geoloc->distance($coordonneeDepart[0], $coordonneeDepart[1], $content['distance']);
        $dateMax = $definirDate->duree($definirDate->aujourdhui(), $content['delai']);
        $interventions = $this->interventionRepository->listeInterInstitutionnel(
            $rayonRecherche[0],
            $rayonRecherche[1],
            $rayonRecherche[2],
            $rayonRecherche[3],

            $dateMax
        );
        
        foreach ($interventions as $intervention) {

            $otdFree = $interRegionDep->dispoOTD($intervention);

            $liste[] = [
                'inter' => [
                    $intervention->getRdvAt()->format('d/m/Y H:i'),
                    $intervention->getAdresse()->getCodePostal() . ' ' . $intervention->getAdresse()->getVille(),
                    $intervention->getListeInter()->getNom() . ' ' . $intervention->getTypeInter()->getNom(),

                ],
                'result' => $otdFree,
                'tipi' => [
                    'typeInter' => $intervention->getTypeInter()->getId(),
                    'listeInter' => $intervention->getListeInter()->getId(),
                    'codeP' => $intervention->getAdresse()->getCodePostal(),
                    'ville' => $intervention->getAdresse()->getVille(),
                    'otd' => $intervention->getReservation()->getSalarie()->getId(),
                    'date' => $intervention->getRdvAt()->getTimestamp()
                ]
            ];
        }


        return new JsonResponse($liste);

    }
}
