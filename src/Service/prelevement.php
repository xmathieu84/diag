<?php


namespace App\Service;


use App\Entity\EntreInter;
use App\Repository\EntrepriseRepository;
use App\Repository\EtatAbonnementRepository;
use App\Repository\InterventionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError as RuntimeErrorAlias;
use Twig\Error\SyntaxError as SyntaxErrorAlias;

class prelevement
{
    /**
     * @var DefinirDate
     */
    private DefinirDate $definirDate;
    /**
     * @var EntrepriseRepository
     */
    private EntrepriseRepository $entrepriseRepository;
    /**
     * @var EtatAbonnementRepository
     */
    private EtatAbonnementRepository $etatAbonnementRepository;
    /**
     * @var InterventionRepository
     */
    private InterventionRepository $interventionRepository;
    /**
     * @var Environment $environment
     */
    private $environment;
    /**
     * @var
     */
    private $mail;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $params;

    /**
     * prelevement constructor.
     * @param DefinirDate $definirDate
     * @param EntrepriseRepository $entrepriseRepository
     * @param EtatAbonnementRepository $etatAbonnementRepository
     * @param InterventionRepository $interventionRepository
     * @param Environment $environment
     * @param Mail $mail
     */
    public function __construct(DefinirDate $definirDate,
                                EntrepriseRepository $entrepriseRepository,
                                EtatAbonnementRepository $etatAbonnementRepository,
                                InterventionRepository $interventionRepository,Environment $environment,Mail $mail,ParameterBagInterface $params)
    {
        $this->definirDate = $definirDate;
        $this->entrepriseRepository=$entrepriseRepository;
        $this->etatAbonnementRepository =$etatAbonnementRepository;
        $this->interventionRepository=$interventionRepository;
        $this->environment = $environment;
        $this->mail = $mail;
        $this->params=$params;
    }

    /**
     * @throws NonUniqueResultException
     * @throws MpdfException
     * @throws LoaderError
     * @throws RuntimeErrorAlias
     * @throws SyntaxErrorAlias
     */
    public function prelevementAbo(){
        $debut = $this->definirDate->debutMoisAvant();
        $date = $this->definirDate->aujourdhui()->format('m/Y');
        $dateAbo = \DateTime::createFromFormat('d/m/Y','15/'.$date);
        setlocale(LC_TIME,['fr','fra','fr_FR']);

        $fin =$this->definirDate->finMoisAvant();
        $entreprises = $this->entrepriseRepository->findAll();
        $liste =[];

        foreach ($entreprises as $entreprise) {
            $totalInter = 0;
            $interventions = $this->interventionRepository->findByInstitution($debut,$fin,$entreprise);
            $etat = $this->etatAbonnementRepository->trouverEtatAdmin($entreprise,$dateAbo,true);

            $entreInter = new EntreInter();
            $entreInter->setEntreprise($entreprise);

            $entreInter->setAbonnement($etat->getMontant());
            foreach ($interventions as $intervention){
                $totalInter += $intervention->getPrix();
            }
            $entreInter->setIntervention($totalInter);
            $liste[] = $entreInter;
        }

        $pdf = new Mpdf();
        $html = $this->environment->render('administrateur/listeVirement.html.twig', [

            'listes'=>$liste

        ]);

        $pdf->WriteHTML($html, 0);

        $content = $pdf->Output('' , "S");

        $this->mail->mailPrelevement($content,strftime('%B'));
        return $liste;
    }


}