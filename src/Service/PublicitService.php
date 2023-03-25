<?php

namespace App\Service;

use App\Entity\Intervention;
use App\Repository\ProBtpRepository;
use phpDocumentor\Reflection\Types\ArrayKey;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PublicitService
{
    private ProBtpRepository $proBtpRepository;
    private Environment $environment;

    public function __construct(ProBtpRepository $proBtpRepository, Environment $environment)
    {
        $this->proBtpRepository = $proBtpRepository;
        $this->environment = $environment;
    }

    /**
     * @param Intervention|null $intervention
     * @return array
     */
    public function emmetrePub(Intervention $intervention)
    {
        if ($intervention) {
            $travaux = $intervention->getTravauxes();
            $liste = [];
            foreach ($travaux as $key => $travail) {

                $proPremiums = $this->proBtpRepository->findForPubcibleInter($travail, $intervention->getAdresse()->getCoordonnees()->getLatitude(), $intervention->getAdresse()->getCoordonnees()->getLongitude(), 'premium', 4);

                foreach ($proPremiums as $premium) {
                    $liste[$travail->getNom()][] = $premium;
                }
                $proPremium2 = $this->proBtpRepository->findForPubcibleInter($travail, $intervention->getAdresse()->getCoordonnees()->getLatitude(), $intervention->getAdresse()->getCoordonnees()->getLongitude(), 'premium');
                foreach ($proPremium2 as $premium2) {

                    if (!in_array($premium2, $liste[$travail->getNom()])) {
                        $liste[$travail->getNom()][] = $premium2;
                    }

                }
                $autres = $this->proBtpRepository->findForPubcibleInter($travail, $intervention->getAdresse()->getCoordonnees()->getLatitude(), $intervention->getAdresse()->getCoordonnees()->getLongitude(), 'classique');
                foreach ($autres as $autre) {
                    $liste[$travail->getNom()][] = $autre;
                }


            };

            return $liste;
        }
        else{
            return null;
        }

    }
}