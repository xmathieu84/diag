<?php

namespace App\Controller;

use App\Helper\EntrepriseRepoTrait;
use App\Helper\SalarieRepoTrait;
use DateTime;

use Exception;
use Mpdf\Mpdf;
use App\Entity\Devis;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use App\Service\choixTemplate;
use App\Helper\EntityManagerTrait;
use App\Repository\DevisRepository;
use App\Service\DefinirAcces;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CreerDevisController extends AbstractController
{
    use RequestTrait, EntityManagerTrait, EntrepriseRepoTrait,SalarieRepoTrait;

    /**
     * @Route("/creer/devis", name="creer_devis")
     * @Security("is_granted('ROLE_ENTREPRISE')")
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @param DefinirAcces $definirAcces
     * @return Response
     * @throws Exception
     */
    public function index(choixTemplate $choixTemplate, DefinirDate $definirDate, DefinirAcces $definirAcces): Response
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $template = $choixTemplate->templateAE($entreprise);
        $date = $definirDate->aujourdhui();
        $nom = $definirAcces->accesSite($entreprise);

        return $this->render('creer_devis/devis.html.twig', [
            'salarie' => $salarie,
            'entreprise' => $entreprise,
            'date' => $date,
            'nom' => $nom
        ]);
    }

    /**
     * @Route("/exportDevis")
     * @Security("is_granted('ROLE_ENTREPRISE')")
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws MpdfException
     */
    public function exportDevis(DefinirDate $definirDate):JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $dirigeant = $this->salarieRepository->findDirirgeant($salarie->getEntreprise());
        $entreprise = $salarie->getEntreprise();
        $reponse = new JsonResponse();
        $date = $definirDate->aujourdhui();
        $pdf = new Mpdf();
        $style = file_get_contents("../public/css/css_cerfa/devis.css");
        $html = $this->renderView('creer_devis/devisPdf.html.twig', [
            'entreprise' => $entreprise,
            'contenu' => $contenu,
            'date' => $date,
            'dirigeant'=>$dirigeant
        ]);
        $nom = $contenu['corpsClient']['nom'] . $contenu['corpsClient']['prenom'] . $contenu['corpsClient']['entreprise'] . '.pdf';
        $chemin = '../public/uploads/devis/' . $nom;

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output($chemin, "F");
        $devis = new Devis();
        $devis->setNom($nom)
            ->setdate($date)
            ->setEntreprise($entreprise);
        $this->manager->persist($devis);
        $this->manager->flush();

        return $reponse->setData($nom);

    }

    /**
     * @Route("/recupererDevis")
     * @Security("is_granted('ROLE_ENTREPRISE')")
     * @param DevisRepository $devisRepository
     * @return JsonResponse
     */
    public function recupereDevis(DevisRepository $devisRepository):JsonResponse
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $contenu = json_decode($this->request->getContent(), true);
        $debut = $contenu['debut'];
        $fin = $contenu['fin'];
        $Debut = DateTime::createFromFormat('!d/m/Y', $debut);
        $Fin = DateTime::createFromFormat('!d/m/Y H:i:s', $fin . '23:59:59');
        $devis = $devisRepository->findByDateEntreprise($entreprise, $Debut, $Fin);

        $reponse = new JsonResponse();
        return $reponse->setData($devis);
    }
}
