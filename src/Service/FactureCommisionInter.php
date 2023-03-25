<?php


namespace App\Service;



use App\Entity\FactureOtd;
use App\Entity\Intervention;
use App\Entity\Reservation;
use App\Entity\User;
use App\Helper\InterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\FactureOtdRepository;
use App\Repository\FacturesRepository;
use App\Repository\PourcentageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mpdf\Mpdf;
use Twig\Environment;

class FactureCommisionInter
{
    use SalarieRepoTrait,InterRepoTrait;
    /**
     * @var Environment
     */
    private Environment $environment;
    /**
     * @var Mail
     */
    private Mail $mail;

    private FactureOtdRepository $factureOtdRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    private DefinirDate $definirDate;
    private PourcentageRepository $repository;
    private FacturesRepository $facturesRepository;
    public function __construct(Environment $environment,FacturesRepository $facturesRepository,Mail $mail,EntityManagerInterface $manager,DefinirDate $definirDate,PourcentageRepository $repository,FactureOtdRepository $factureOtdRepository)
    {
        $this->mail=$mail;
        $this->environment=$environment;
        $this->manager=$manager;
        $this->definirDate = $definirDate;
        $this->repository = $repository;
        $this->factureOtdRepository = $factureOtdRepository;
        $this->facturesRepository=$facturesRepository;
    }

    /**
     * @param Reservation $reservation
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Mpdf\MpdfException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function comInter($intervention,string $type){

        $pdf = new Mpdf();
        $factures = $this->factureOtdRepository->findAll();
        $compte = $this->definirDate->aujourdhui()->format('Y-m');
        $tauxTva = $this->repository->findOneBy(['nom'=>'tva']);
        $html = $this->environment->render('pdf/factureInterCommision.html.twig', [
            'intervention' => $intervention,
            'numero'=>$compte.'-'.count($factures)+1,
            'aujour'=>$this->definirDate->aujourdhui(),
            'tva'=>$tauxTva->getTaux(),
            'taux'=>1+$tauxTva->getTaux()/100,
            'type'=>$type
        ]);
        $pdf->WriteHTML($html, 0);
        $facture = $pdf->Output('','S');
        if ($type ==="drone"){
            $dirigeant = $this->salarieRepository->findDirirgeant($intervention->getPropositionChoisie()->getSalarie()->getEntreprise());
            $entreprise =  $intervention->getPropositionChoisie()->getSalarie()->getEntreprise();
        }
        else{
            $dirigeant = $this->salarieRepository->findDirirgeant($intervention->getOdi()->getEntreprise());
            $entreprise = $intervention->getOdi()->getEntreprise();
        }
        $mail = $dirigeant->getUser()->getEmail();
        $this->mail->mailFactureInter($facture,$mail,$intervention,$type);
        $nom = 'intervention'.time().'.pdf';
        $pdf->Output('../public/uploads/factureDD/'.$nom,'F');
        $facture = new FactureOtd();
        $facture->setDate($this->definirDate->aujourdhui())
            ->setNom($nom)
            ->setEntreprise($entreprise)
            ->setType('commission');
        $this->manager->persist($facture);
    }

    public function factureAccompte(Intervention $intervention){
        $pdf = new Mpdf();
        $date = new \DateTime('NOW');
        $interventions = $this->interventionRepository->findAll();
        $factures = $this->facturesRepository->findAll();
        $mois = $date->format('n');
        $annee = $date->format('Y');
        $nom = 'interAcc'.$annee.'-'.$mois.'-'.(count($factures)+1).'.pdf';
        $tauxTva = $this->repository->findOneBy(['nom'=>'tva']);
        $entreprise = $intervention->getPropositionChoisie()->getSalarie()->getEntreprise();
        if ($entreprise->getSiretTva()->getAssujeti() === true){
            $taux = 1+$tauxTva->getTaux()/100;
        }
        else{
            $taux = 1;
        }
        $acompte = $this->repository->findOneBy(['nom'=>'acompte']);
        $dirigeant = $this->salarieRepository->findDirirgeant($entreprise);
        $html = $this->environment->render('pdf/factureAcompte.html.twig', [
            'intervention' => $intervention,
            'numero'=> 'interAcc' . $annee.'-'.$mois.'-'.(count($factures)+1),
            'aujour'=>$this->definirDate->aujourdhui(),
            'tva'=>$tauxTva->getTaux(),
            'taux'=>$taux,
            'entreprise'=>$intervention->getPropositionChoisie()->getSalarie()->getEntreprise(),
            'dirigeant'=>$dirigeant,
            'acompte'=>$acompte->getTaux(),
            'nom'=>$nom
        ]);
        $pdf->WriteHTML($html, 0);

        $pdf->Output('../public/uploads/factureDD/'.$nom,'F');
        return $nom;

    }

    public function factureIntervention(Intervention $intervention,User $user){
        $pdf = new Mpdf();
        $date = new \DateTime('NOW');

        $factures = $this->facturesRepository->findAll();
        $mois = $date->format('n');
        $annee = $date->format('Y');
        $nom = 'inter'.$annee.'-'.$mois.'-'.(count($factures)+1).'.pdf';
        $tauxTva = $this->repository->findOneBy(['nom'=>'tva']);
        $entreprise = $intervention->getPropositionChoisie()->getSalarie()->getEntreprise();
        if ($entreprise->getSiretTva()->getAssujeti() === true){
            $taux = 1+$tauxTva->getTaux()/100;
        }
        else{
            $taux = 1;
        }
        if ($user->hasRole('ROLE_DEMANDEUR') === true){
            $type = true;
        }
        else{
            $type = false;
        }

        $acompte = $this->repository->findOneBy(['nom'=>'acompte']);
        $dirigeant = $this->salarieRepository->findDirirgeant($entreprise);
        $html = $this->environment->render('pdf/factureIntervention.html.twig', [
            'intervention' => $intervention,
            'numero'=> 'inter' . $annee.'-'.$mois.'-'.(count($factures)+1),
            'aujour'=>$this->definirDate->aujourdhui(),
            'tva'=>$tauxTva->getTaux(),
            'taux'=>$taux,
            'entreprise'=>$intervention->getPropositionChoisie()->getSalarie()->getEntreprise(),
            'dirigeant'=>$dirigeant,
            'acompte'=>$acompte->getTaux(),
            'nom'=>$nom,
            'type'=>$type
        ]);
        $pdf->WriteHTML($html, 0);

        $pdf->Output('../public/uploads/factureDD/'.$nom,'F');
        return $nom;

    }
}