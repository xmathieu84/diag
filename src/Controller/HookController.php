<?php

namespace App\Controller;

use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\BanqueRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\KycDeclarationRepository;
use App\Repository\MandatCerfaRepository;
use App\Repository\UboDeclarationRepository;
use App\Repository\UserRepository;
use App\Service\DefinirDate;
use App\Service\FacturePdfService;
use App\Service\Fichier;
use App\Service\Yousign;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HookController extends AbstractController
{
    use EntityManagerTrait;
    use etatAboRepoTrait;
    use BanqueRepoTrait;
    use SalarieRepoTrait;
    use AgentRepoTrait;
    use EntrepriseRepoTrait;
    use RequestTrait;
    use AboTotalInstiRepoTrait;

    /**
     * @Route("/retourSepa/{id}")
     */
    public function retourSepa(Yousign $yousign, Fichier $fichier, $id, DefinirDate $definirDate, UserRepository $repository, FacturePdfService $facturePdfService)
    {
        $content = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

        $file = $content['procedure']['files'][0]['id'];

        $data = $yousign->telechargerFichier($file);
        $nom = 'sepa'.time().'.pdf';

        file_put_contents($nom, base64_decode($data));
        $fichier->deplacer($nom, '../public/uploads/sepa/'.$nom);
        $user = $repository->findOneBy(['id' => $id]);
        $salarie = $this->salarieRepository->findOneBy(['user' => $user]);
        $agent = $this->agentRepository->findOneBy(['user' => $user]);

        if ($agent) {
            $debiteur = $agent->getDemandeur();
            $abonnements = $this->aboTotalInstiRepository->findAbonnement($agent->getDemandeur(), $definirDate->aujourdhuiImmutable());
            foreach ($abonnements as $abonnement) {
                $facturePdfService->factureAbonnementGci($agent, $abonnement);
            }
            $banque = $this->banqueRepository->findOneBy(['institution' => $debiteur, 'actif' => true]);
            $banque->setSepaSigne($nom);
            $this->manager->flush();
        } else {
            $debiteur = $salarie->getEntreprise();
            $etat = $this->etatAbonnementRepository->trouverEtat($debiteur, $definirDate->aujourdhui());
            $facturePdfService->factureAbonnementOTD($etat, $definirDate->aujourdhui());
            $banque = $this->banqueRepository->findOneBy(['entreprise' => $debiteur, 'actif' => true]);
            $etat->setAbonne(true);
            $this->manager->persist($etat);
            if (!$banque->getSepaSigne()) {
                $dureeAbonnement = $etat->getDateDebut()->diff($etat->getDatefin());
                $etat->setDateDebut($definirDate->aujourdhuiImmutable());
                $etat->setDatefin($etat->getDateDebut()->add($dureeAbonnement));
                $this->manager->persist($etat);
                $this->manager->flush();
            }

            $banque->setSepaSigne($nom);

            foreach ($salarie->getEntreprise()->getSalaries() as $salary) {
                if ($salary->getUser()->hasRole('ROLE_FREE')) {
                    $salary->getUser()->removeRole('ROLE_FREE');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                } elseif ($salary->getUser()->hasRole('ROLE_CLASSIC')) {
                    $salary->getUser()->removeRole('ROLE_CLASSIC');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                } elseif ($salary->getUser()->hasRole('ROLE_PREMIUM')) {
                    $salary->getUser()->removeRole('ROLE_PREMIUM');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                } elseif ($salary->getUser()->hasRole('ROLE_INFINITE')) {
                    $salary->getUser()->removeRole('ROLE_INFINITE');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                }
                if ('So free' === $etat->getAbonnement()->getNom()) {
                    $salary->getUser()->addRole('ROLE_FREE');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                } elseif ('Classic access' === $etat->getAbonnement()->getNom()) {
                    $salary->getUser()->addRole('ROLE_CLASSIC');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                } elseif ('Premium network' === $etat->getAbonnement()->getNom()) {
                    $salary->getUser()->addRole('ROLE_PREMIUM');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                } elseif ('Infinite network' === $etat->getAbonnement()->getNom()) {
                    $salary->getUser()->addRole('ROLE_INFINITE');
                    $this->manager->persist($salary);
                    $this->manager->flush();
                }
            }
        }

        $this->manager->persist($banque);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @param $id
     *
     * @throws \JsonException
     * @Route("/retourMandatCerfa/{id}")
     */
    public function retourMandatCerfa($id, MandatCerfaRepository $repository, Yousign $yousign, Fichier $fichier)
    {
        $content = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

        $file = $content['procedure']['files'][0]['id'];

        $data = $yousign->telechargerFichier($file);
        $nom = 'mandatCerfa'.time().'.pdf';
        $mandat = $repository->findOneBy(['id' => $id]);
        file_put_contents($nom, base64_decode($data));
        $fichier->deplacer($nom, '../public/uploads/mandatCerfa/'.$nom);
        $mandat->setMandatSigne($nom);
        exit;
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     * @Route("/accordUbo")
     */
    public function UboAccepted(DefinirDate $definirDate, UboDeclarationRepository $uboRepo)
    {
        $ubo = $uboRepo->findOneBy(['idUbo' => $_GET['RessourceId']]);
        $salarie = $this->salarieRepository->findDirirgeant($ubo->getEntreprise());
        $date = $definirDate->aujourdhui();
        $ubo->setResultat('acceptée')
            ->setDateReponse($date);
        $this->manager->persist($ubo);
        $this->manager->flush();
        exit;
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     * @Route ("/refusUbo")
     */
    public function UboRefused(DefinirDate $definirDate, UboDeclarationRepository $uboRepo)
    {
        $ubo = $uboRepo->findOneBy(['idUbo' => $_GET['RessourceId']]);
        $date = $definirDate->aujourdhui();
        $ubo->setResultat('refusée')
            ->setDateReponse($date);
        $this->manager->persist($ubo);
        $this->manager->flush();
        exit;
    }

    /**
     * @return void
     * @Route("/refusKyc")
     */
    public function kycRefused(DefinirDate $definirDate, KycDeclarationRepository $repository)
    {
        $kyc = $repository->findOneBy(['idKyc' => $_GET['RessourceId']]);
        $kyc->setReponse('refusée')
            ->setDateReponse($definirDate->aujourdhui());
        $this->manager->persist($kyc);
        $this->manager->flush();

        exit;
    }

    /**
     * @return void
     * @Route("/accordKyc")
     */
    public function kycAccepted(DefinirDate $definirDate, KycDeclarationRepository $repository)
    {
        $kyc = $repository->findOneBy(['idKyc' => $_GET['RessourceId']]);
        $kyc->setReponse('acceptée')
            ->setDateReponse($definirDate->aujourdhui());
        $this->manager->persist($kyc);
        $this->manager->flush();

        exit;
    }
}
