<?php


namespace App\Controller\scriptCron;


use App\Entity\Proposition;

use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\InterventionRepository;
use App\Repository\SalarieRepository;
use App\Service\DefinirDate;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class VerifieInterCron
{
    private DefinirDate $definirDate;
    private Mail $mail;
private SalarieRepository $salarieRepository;
private InterventionRepository $interventionRepository;
private  EntityManagerInterface $manager;

    public function __construct(DefinirDate $definirDate,Mail $mail,SalarieRepository $salarieRepository,
                                InterventionRepository $interventionRepository,EntityManagerInterface $manager)
    {
        $this->salarieRepository=$salarieRepository;
        $this->interventionRepository=$interventionRepository;
        $this->manager=$manager;
        $this->definirDate=$definirDate;
        $this->mail =$mail;
    }

    public  function interSansPropCron(){
        $date = $this->definirDate->aujourdhui();
        $delai = $this->definirDate->duree($date, 'P7D');
        $interventions = $this->interventionRepository->interSansProp($delai);


        foreach ($interventions as $intervention) {
            if ($intervention->getPropositions()->isEmpty()) {
                $interventionLat = $intervention->getAdresse()->getCoordonnees()->getLatitude();
                $interventionLon = $intervention->getAdresse()->getCoordonnees()->getLongitude();
                $maintenant = $this->definirDate->aujourdhui();
                $demain = $this->definirDate->duree($maintenant, 'P1D');
                $dateInter = $intervention->getRdvAT();

                $salaries = $this->salarieRepository->choixSalariePourIntervention(
                    $intervention->getListeInter()->getId(),
                    $intervention->getTypeInter()->getId(),
                    $intervention->getId(),
                    $dateInter,
                    $interventionLat,
                    $interventionLon
                );

                foreach ($salaries as $salarie) {
                    $proposition = new Proposition();
                    $proposition->setSalarie($salarie)
                        ->setIntervention($intervention)
                        ->setDateFin($demain);
                    $this->manager->persist($proposition);
                    $this->manager->flush();
                    $adresseMail = $this->mail->getMail($salarie->getId());

                     $this->mail->mailInter($adresseMail);
                }
            }
        }


    }
}