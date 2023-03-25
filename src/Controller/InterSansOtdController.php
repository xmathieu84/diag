<?php

namespace App\Controller;

use App\Entity\Proposition;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\DefinirDate;
use App\Service\Mail;
use Symfony\Component\Routing\Annotation\Route;

class InterSansOtdController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    use InterRepoTrait,EntityManagerTrait,SalarieRepoTrait;

    /**
     * @param DefinirDate $definirDate
     * Tache planifiée
     * @param Mail $mail
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @Route ("/InterPeuOtd/0aUQau5UK8lgNaDqUvKXj4uJ1vKV09Ef")
     */
    public function InterPeuOtd(DefinirDate $definirDate,Mail $mail){
        $date = $definirDate->duree($definirDate->aujourdhui(),'P3D');
        $interventions = $this->interventionRepository->verificationIntervention($date);
        
        foreach ($interventions as $intervention){
            $listeSal=[];

            if (count($intervention->getPropositions())<3) {
                $salaries = $this->salarieRepository->findGlobal($intervention, $intervention->getRdvAt());


                foreach ($intervention->getPropositions() as $proposition) {
                    $listeSal[] = $proposition->getSalarie()->getId();
                }
                foreach ($salaries as $salarie) {
                    if (!in_array($salarie->getId(), $listeSal, true)) {
                        $proposition = new Proposition();
                        $proposition->setSalarie($salarie)
                            ->setInter($intervention)
                            ->setDateFin($definirDate->duree($definirDate->aujourdhui(), 'P1D'));
                        $this->manager->persist($proposition);

                        $mail->mailDemandeIntervention($salarie->getUser()->getEmail());
                    }
                }
            }
        }
        $this->manager->flush();
        exit();
    }

}