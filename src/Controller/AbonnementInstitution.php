<?php


namespace App\Controller;


use App\Entity\AboTotalInsti;
use App\Entity\FactureInsti;
use App\Entity\PackSupAboInsti;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\AboTotalInstiRepository;
use App\Repository\FactureInstiRepository;
use App\Repository\PackSupRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use DateTimeImmutable;
use Mpdf\Mpdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AbonnementInstitution extends AbstractController
{
    use AgentRepoTrait,RequestTrait,EntityManagerTrait,AbonnementGcirepoTrait;

    /**
     * @param PackSupRepository $packSupRepository
     * @param AboTotalInstiRepository $aboTotalInstiRepository
     * @return Response
     * @Route ("/Mon abonnement/{code}",name="monAbonnement")
     * @Security("is_granted('ROLE_MANITOU') or is_granted('ROLE_BTP')")
     */
    public function packAbo(PackSupRepository $packSupRepository,AboTotalInstiRepository $aboTotalInstiRepository,string $code=null){


        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $aboTotal = $aboTotalInstiRepository->findBy(['demandeur'=>$agent->getDemandeur(),'abonne'=>true]);
        $aboTotalSouscrit = $aboTotalInstiRepository->findAbonnementClassique($agent->getDemandeur());
        $abonnementAPrendre = $this->abonnementGciRepository->findOneBy(['profil'=>$agent->getDemandeur()->getProfil(),'utlisateur'=>2]);
        if ( $this->getUser()->hasRole('ROLE_INSTITUTION')){
            $packs = $packSupRepository->findByHabitant($aboTotal[0]->getAbonnement()->getProfil(),$aboTotal[0]->getAbonnement()->getLimiteH(),$aboTotal[0]->getAbonnement()->getLimiteB());
        }
        else{
            $packs = $packSupRepository->findBy(['cible'=>'gc','profil'=>$agent->getDemandeur()->getProfil()]);
        }
        return $this->render('institution/packAbo.html.twig',[
            'packs'=>$packs,
            'agent'=>$agent,
            'aboTotals'=>$aboTotal,
            'code'=>$code,
            'aboTotalSouscrit'=>$aboTotalSouscrit,
            "abonnementAPrendre"=>$abonnementAPrendre
        ]);
    }

    /**
     * @param PackSupRepository $packSupRepository
     * @param AboTotalInstiRepository $aboTotalInstiRepository
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @Route ("/institution/ajoutPack")
     * @Security("is_granted('ROLE_MANITOU')")
     */
    public function ajoutPack(PackSupRepository $packSupRepository,AboTotalInstiRepository $aboTotalInstiRepository,DefinirDate $definirDate,FactureInstiRepository $factureInstiRepository):JsonResponse{
        $content = json_decode($this->request->getContent(),true);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $factures = $factureInstiRepository->findAll();
        $aboTotal = $aboTotalInstiRepository->findOneBy(['demandeur'=>$agent->getDemandeur(),'abonne'=>true]);
        $dateEmiisionFacture = $definirDate->duree($definirDate->aujourdhui(),'P14D');

        if ($dateEmiisionFacture->format('d') < $aboTotal->getDebut()->format('d')){
            $date = $definirDate->aujourdhuiImmutable();
            $nom = count($factures).'GCI.pdf';
            $facture = new FactureInsti();
            $facture->setNom($nom)
                ->setDate($date)
                ->setInstitution($agent->getDemandeur());
            $total = 0;
            foreach ($content['packCommande'] as $packEnvoie){
                $pack = $packSupRepository->findOneBy(['id'=>$packEnvoie['id']]);
                $total += $packEnvoie['prix'];
                for ($i=0;$i<$packEnvoie['nombre'];$i++){
                    $packSupAbo = new PackSupAboInsti();
                    $packSupAbo->setAboInsti($aboTotal)
                        ->setPackSup($pack)
                        ->setFin($aboTotal->getFin())
                        ->setFactureInsti($facture);
                    $this->manager->persist($packSupAbo);
                }
            }
            $pdf = new Mpdf();
            $html = $this->renderView('pdf/factureInsti.html.twig',[
                'packs'=>$content['packCommande'],
                'date'=>$date,
                'demandeur'=>$agent->getDemandeur(),
                'total'=>$total,
                'numero'=>$date->format("Y-m").'-'.(count($factures)+1)
            ]);
            $pdf->WriteHTML($html,0);
            $pdf->Output('../public/uploads/factureInsti/'.$nom,'F');
            $this->manager->persist($facture);
            $this->manager->flush();
            $fact = true;
        }
        else{
            $fact = false;
        }
        $response = new JsonResponse();

        return $response->setData(['fact'=>$fact]);
    }

    /**
     * @return Response
     * @Route("/factures d'abonnement/{code}",name="factureAbonnement")
     * @Security("is_granted('ROLE_MANITOU') or is_granted('ROLE_BTP')")
     */
    public function factureAbonnement(choixTemplate $choixTemplate,string $code=null):Response{


        return $this->render('institution/factureAbonnement.html.twig',['code'=>$code]);
    }

    /**
     * @param FactureInstiRepository $factureInstiRepository
     * @return JsonResponse
     * @Route("/retrouveFactureInsti")
     * @Security("is_granted('ROLE_MANITOU') or is_granted('ROLE_BTP')")
     */
    public function retrouveFactureInsti(FactureInstiRepository $factureInstiRepository):JsonResponse{

        $periode = json_decode($this->request->getContent(), true);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $fact = [];
        $debut = $periode['debut'];
        $fin = $periode['fin'].'23:59:59';

        $Debut = DateTimeImmutable::createFromFormat('!d/m/Y', $debut);
        $Fin = DateTimeImmutable::createFromFormat('!d/m/Y H:i:s', $fin);
        $factures = $factureInstiRepository->findByDateInsti($agent->getDemandeur(),$Debut,$Fin);
        foreach ($factures as $facture){
            $fact[] = $facture->getNom();
        }

        $response = new JsonResponse();
        return $response->setData(['facture'=>$fact]);
    }
}