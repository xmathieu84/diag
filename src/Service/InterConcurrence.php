<?php


namespace App\Service;


use App\Entity\Coordonnees;
use App\Entity\Intervention;

use App\Entity\Salarie;
use App\Helper\SalarieRepoTrait;
use App\Repository\FichierOTDRepository;
use DateTimeInterface;

class InterConcurrence
{
    use SalarieRepoTrait;
    private Geoloc $geoloc;
    private FichierOTDRepository $fichierOTDRepository;

    /**
     * InterConcurrence constructor.
     * @param FichierOTDRepository $fichierOTDRepository
     * @param Geoloc $geoloc
     */
    public function __construct(FichierOTDRepository $fichierOTDRepository,Geoloc $geoloc)
    {
        $this->fichierOTDRepository=$fichierOTDRepository;
        $this->geoloc = $geoloc;
    }

    /**
     * @param Intervention $intervention
     * @param DateTimeInterface|null $rdv
     * @return int
     */
    public function concurrence(Intervention $intervention):int{
        $salaries= $this->salarieRepository->findGlobal($intervention);

        if (count($salaries)<3){
            $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),50);
            $otdSup = $this->salarieRepository->findOtdSup($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
            $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
            $nombreOtd = count($salarieTotal);

            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                $otdSup = $this->salarieRepository->findOtdSup($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);


            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),100);
                $otdSup = $this->salarieRepository->findOtdSup($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                $otdSup = $this->salarieRepository->findOtdSup($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),200);
                $otdSup = $this->salarieRepository->findOtdSup($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordonnee = $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                $Otd = $this->fichierOTDRepository->findGlobal($coordonnee[0],$coordonnee[1],$coordonnee[2],$coordonnee[3]);
                $nombreOtd = count($salarieTotal) + count($Otd);

            }
            if ($nombreOtd<3){
                $coordonnee = $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                $Otd = $this->fichierOTDRepository->findGlobal($coordonnee[0],$coordonnee[1],$coordonnee[2],$coordonnee[3]);
                $nombreOtd = count($salarieTotal) + count($Otd);
            }

        }
        else{
            $nombreOtd = count($salaries);
        }
        return $nombreOtd;
    }

    /**
     * @param Intervention $intervention
     * @param DateTimeInterface $date
     * @return int
     */
    public function concurrenceTotale(Intervention $intervention,DateTimeInterface $date){
        $salaries= $this->salarieRepository->findGlobal($intervention);

        if (sizeof($salaries)<3){
            $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),50);
            $otdSup = $this->salarieRepository->findOtdSup($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
            $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
            $nombreOtd = sizeof($salarieTotal);

            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);


            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),100);
                $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),200);
                $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordonnee = $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                $Otd = $this->fichierOTDRepository->findGlobal($coordonnee[0],$coordonnee[1],$coordonnee[2],$coordonnee[3]);
                $nombreOtd = sizeof($salarieTotal) + sizeof($Otd);

            }
            if ($nombreOtd<3){
                $coordonnee = $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                $Otd = $this->fichierOTDRepository->findGlobal($coordonnee[0],$coordonnee[1],$coordonnee[2],$coordonnee[3]);
                $nombreOtd = sizeof($salarieTotal) + sizeof($Otd);
            }

        }
        else{
            $nombreOtd = sizeof($salaries);
        }
        return $nombreOtd;
    }

    /**
     * @param array $interventions
     * @param Salarie $salarie
     * @param Coordonnees $coordonnees
     * @return array
     */
    public function nbreInterSansProp( array $interventions,Salarie $salarie,Coordonnees $coordonnees){
        $interSansProp=[];
        foreach ($interventions as $intervention) {
            if (count($intervention->getPropositions())===0){

                if (count($intervention->getPropositions())<3 && $intervention->getIntDem()) {
                    $distance = $this->geoloc->coutKilometre(
                        $intervention->getAdresse()->getCoordonnees()->getLatitude(),
                        $intervention->getAdresse()->getCoordonnees()->getLongitude(),
                        $coordonnees->getLatitude(),
                        $coordonnees->getLongitude()
                    );
                    $interSansProp[] = [$intervention, $distance / 1000];
                }
            }
            else{
                foreach ($intervention->getPropositions() as $proposition){
                    if ($proposition->getSalarie()->getEntreprise() !== $salarie->getEntreprise()){
                        if (count($intervention->getPropositions())<3 && $intervention->getIntDem()) {
                            $distance = $this->geoloc->coutKilometre(
                                $intervention->getAdresse()->getCoordonnees()->getLatitude(),
                                $intervention->getAdresse()->getCoordonnees()->getLongitude(),
                                $coordonnees->getLatitude(),
                                $coordonnees->getLongitude()
                            );
                            $interSansProp[] = [$intervention, $distance / 1000];
                        }
                    }
                }
            }

        }
        $inter = array_column($interSansProp, 1);
        array_multisort($inter, SORT_NUMERIC, SORT_ASC, $interSansProp);

        return $interSansProp;
    }

    /**
     * @param Intervention $intervention
     * @return int
     */
    public function interConcurrenceRapideGlobal(Intervention $intervention){
        $salaries = $this->salarieRepository->findOtdRapideGlobal($intervention);

        if (count($salaries)<3){
            $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),50);
            $otdSup = $this->salarieRepository->findOtdSupRapideGlobal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
            $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
            $nombreOtd = count($salarieTotal);

            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                $otdSup = $this->salarieRepository->findOtdSupRapideGlobal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);


            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),100);
                $otdSup = $this->salarieRepository->findOtdSupRapideGlobal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                $otdSup = $this->salarieRepository->findOtdSupRapideGlobal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),200);
                $otdSup = $this->salarieRepository->findOtdSupRapideGlobal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3]);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = count($salarieTotal);

            }

        }
        else{
            $nombreOtd = count($salaries);
        }
        return $nombreOtd;
    }

    public function concurrenceOtdRapideSup(Intervention $intervention, DateTimeInterface $date):int{
        $salaries = $this->salarieRepository->findOtdRapide($intervention,$date);

        if (sizeof($salaries)< 3){
            $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),50);
            $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
            $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
            $nombreOtd = sizeof($salarieTotal);

            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);


            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),100);
                $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);

            }
            if ($nombreOtd<3){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),200);
                $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$date);
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                $nombreOtd = sizeof($salarieTotal);

            }
        }
        else{
            $nombreOtd = sizeof($salaries);
        }
        return $nombreOtd;
    }

}