<?php

namespace App\Controller;


use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;

use App\Helper\TauxHoraireRepoTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\choixTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TarifController extends AbstractController
{
    use RequestTrait, EntityManagerTrait, TauxHoraireRepoTrait, SalarieRepoTrait,EntrepriseRepoTrait;

    /**
     * @Route("/entreprise/tauxHoraire",name="tarifA")
     *
     * @Security ("is_granted('ROLE_SALARIE')")
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function tarifA(choixTemplate $choixTemplate): Response
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $template = $choixTemplate->templateAE($salarie->getEntreprise());
        $salaries = $salarie->getEntreprise()->getSalaries();

        return $this->render('entreprise/tarifA.html.twig', [
            'template' => $template,
            'entreprise' => $salarie->getEntreprise(),
            'salaries' => $salaries

        ]);
    }

    /**
     * @Route("/envoieTarif")
     * @Security ("is_granted('ROLE_SALARIE')")
     * @return JsonResponse
     */
    public function envoieTarif()
    {
        $contenu = json_decode($this->request->getContent(), true);
        $id = $contenu['id'];
        $tarif = $contenu['tarif'];

        $tauxHoraire = $this->tauxHoraireRepository->findOneBy(['id' => $id]);
        $tauxHoraire->setTaux((float)$tarif);
        $this->manager->persist($tauxHoraire);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/envoiePrixMin")
     * @Security ("is_granted('ROLE_SALARIE')")
     * @return JsonResponse
     * @throws \JsonException
     */
    public function envoiePrixMin():JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $id = $contenu['id'];

        $prixMinimum = $contenu['prixMinimum'];
        $tauxHoraire = $this->tauxHoraireRepository->findOneBy(['id' => $id]);

        $tauxHoraire->setPrixMinimum((float)$prixMinimum);
        $this->manager->persist($tauxHoraire);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/indemnite")
     * @Security ("is_granted('ROLE_SALARIE')")
     * @return JsonResponse
     */
    public function indemnite()
    {
        $indemnite = $this->request->getContent();
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $entreprise->setIndeminiteKilometre($indemnite);
        $this->manager->persist($entreprise);
        $this->manager->flush();

        return new JsonResponse();
    }
}
