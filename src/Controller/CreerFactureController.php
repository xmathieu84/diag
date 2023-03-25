<?php

namespace App\Controller;

use App\Entity\Factures;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\FactureOtdRepository;
use Mpdf\Mpdf;
use App\Service\DefinirDate;
use App\Service\choixTemplate;
use App\Helper\SalarieRepoTrait;
use App\Service\DefinirAcces;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class CreerFactureController
 * @package App\Controller
 */
class CreerFactureController extends AbstractController
{
    use SalarieRepoTrait, RequestTrait, EntityManagerTrait,EntrepriseRepoTrait;

    /**
     * @Route("/creer/facture/{id}", name="creer_facture")
     *
     * @Security("is_granted('ROLE_SALARIE')")
     * @param choixTemplate $choixTemplate
     * @param null $id
     * @param DefinirDate $definirDate
     * @param DefinirAcces $definirAcces
     * @return Response
     */
    public function index(choixTemplate $choixTemplate, DefinirDate $definirDate, DefinirAcces $definirAcces, $id = null):Response
    {

        $template = $choixTemplate->templateSalEnt($this->getUser());

        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $date = $definirDate->aujourdhui();
        $nom = $definirAcces->accesSite($entreprise);

        return $this->render('creer_facture/facture.html.twig', [
            'salarie' => $salarie,
            'entreprise' => $entreprise,
            'date' => $date,
            'id' => $id,
            'nom' => $nom
        ]);
    }

    /**
     * @Route("/exportFacture")
     * @Security("is_granted('ROLE_SALARIE')")
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws MpdfException
     */
    public function exportFacture(DefinirDate $definirDate):JsonResponse
    {
        $user = $this->getUser();
        $contenu = json_decode($this->request->getContent(), true);
        $entreprise = $user->getSalarie()->getEntreprise();
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $dirigeant = $this->salarieRepository->findDirirgeant($salarie->getEntreprise());
        $reponse = new JsonResponse();
        $date = $definirDate->aujourdhui();
        $pdf = new Mpdf();
        $style = file_get_contents("../public/css/css_cerfa/devis.css");
        $html = $this->renderView('creer_facture/facturePdf.html.twig', [
            'entreprise' => $entreprise,
            'contenu' => $contenu,
            'date' => $date,
            'dirigeant'=>$dirigeant
        ]);


        $nom = $contenu['corpsClient']['nom'] . $contenu['corpsClient']['prenom'] . $contenu['corpsClient']['entreprise'] . '.pdf';
        $chemin = '../public/uploads/factureEnt/' . $nom;

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output($chemin, "F");
        $facture = new Factures();
        $facture->setEntreprise($entreprise)
            ->setNom($nom)
            ->setDate($date);

        $this->manager->persist($facture);
        $this->manager->flush();

        return $reponse->setData($nom);
    }

    /**
     * @param FactureOtdRepository $factureOtd
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route ("/facturesReçues",name="factureRecu")
     * @Security("is_granted('ROLE_SALARIE') and is_granted('ROLE_FREE') or is_granted('ROLE_ODI')")
     */
    public function factureDD(FactureOtdRepository $factureOtd){
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $facture = $factureOtd->findBy(['entreprise'=>$entreprise],['date'=>'DESC']);

        return $this->render('entreprise/listeFacture.html.twig',[
            'entreprise'=>$entreprise,
            'factures'=>$facture
        ]);


}
}
