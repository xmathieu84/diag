<?php

namespace App\Service;

use App\Repository\InterventionRepository;

/**
 * Class listeInterDepartementRegion
 * @package App\Service
 */
class listeInterDepartementRegion
{
    /**
     * @var InterventionRepository
     */
    private $interventionRepository;
    /**
     * @var InterRegionDep
     */
    private $interRegionDep;

    /**
     * listeInterDepartementRegion constructor.
     * @param InterventionRepository $interventionRepository
     * @param InterRegionDep $interRegionDep
     */
    public function __construct(InterventionRepository $interventionRepository, InterRegionDep $interRegionDep)
    {
        $this->interventionRepository = $interventionRepository;
        $this->interRegionDep = $interRegionDep;
    }

    /**
     * @param array $codePostaux
     * @param \DateTimeInterface $dateDebut
     * @param \DateTimeInterface $dateFin
     * @return array
     */
    public function getInter(array $codePostaux,\DateTimeInterface $dateDebut,\DateTimeInterface $dateFin)
    {
        $interventions = [];
        foreach ($codePostaux as $codePostal) {

            $inters = $this->interventionRepository->findByCodePostaux($codePostal, $dateDebut, $dateFin);

            foreach ($inters as $inter) {
                $otdFree = $this->interRegionDep->dispoOTD($inter);
                array_push($interventions, [
                    'inter' => [
                        $inter->getRdvAt()->format('d/m/Y H:i'),
                        $inter->getAdresse()->getCodePostal() . ' ' . $inter->getAdresse()->getVille(),
                        $inter->getListeInter()->getNom() . ' ' . $inter->getTypeInter()->getNom(),

                    ],
                    'result' => $otdFree,
                    'tipi' => [
                        'typeInter' => $inter->getTypeInter()->getId(),
                        'listeInter' => $inter->getListeInter()->getId(),
                        'codeP' => $inter->getAdresse()->getCodePostal(),
                        'ville' => $inter->getAdresse()->getVille(),
                        'otd' => $inter->getReservation()->getSalarie()->getId(),
                        'date' => $inter->getRdvAt()->format('d/m/Y H:i')
                    ]
                ]);
            }
        }
        return array_unique($interventions, 4);
    }
}
