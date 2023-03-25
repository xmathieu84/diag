<?php

namespace App\Service;

use DateTime;
use DateInterval;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;


class DefinirDate
{

    /**
     * @return DateTime
     * @throws Exception
     */
    public function aujourdhui():DateTime
    {
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        return $date;
    }

    /**
     * @return DateTimeImmutable
     * @throws Exception
     */
    public function aujourdhuiImmutable(): DateTimeImmutable
    {
        $date = new DateTimeImmutable('NOW', new \DateTimeZone('Europe/Paris'));
        return $date;
    }

    /**
     * Renvoie une date avec la durée indiquée
     *
     *
     * @param string $interval
     * @return DateTimeInterface
     * @throws Exception
     */
    public function duree($dateFin, string $interval)
    {

        $duree = new DateInterval($interval);
        $dateFin->add($duree);
        return $dateFin;
    }

    /**
     * Undocumented function
     *
     * @param DateTime $date
     * @param string $interval
     * @return DateTimeInterface
     * @throws Exception
     */
    public function subDuree(DateTime $date, string $interval):DateTimeInterface
    {
        $duree = new DateInterval($interval);
        $date->sub($duree);
        return $date;
    }

    /**
     * @return DateTimeInterface
     * @throws Exception
     */
    public function finDuMois(): DateTimeInterface
    {
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $dateFin = $date->format('t/m/Y 23:59:59');
        $dateFIN = DateTime::createFromFormat('j/m/Y H:i:s', $dateFin);
        return $dateFIN;
    }

    /**
     * @return DateTime|false
     * @throws Exception
     */
    public function debutDuMois(): DateTime
    {
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $dateDeb = $date->format('01/m/Y 00:00:00');

        $dateDebut = DateTime::createFromFormat('j/m/Y H:i:s', $dateDeb);
        return $dateDebut;
    }

    /**
     * @return DateTime|false
     * @throws Exception
     */
    public function debutJournee(): DateTime
    {
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $dateDebut = $date->format('d/m/Y 00:00:00');
        $dateDebutJour = DateTime::createFromFormat('j/m/Y H:i:s', $dateDebut);

        return $dateDebutJour;
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function finDeJournee(): DateTime
    {
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $dateFin = $date->format('d/m/Y 23:59:59');
        $dateFinJour = DateTime::createFromFormat('j/m/Y H:i:s', $dateFin);
        return $dateFinJour;
    }

    /**
     * Retourne la date de debut d'annee
     *
     * @return DateTime
     */
    public function DebutAnnee(): DateTime
    {
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $dateDeb = $date->format('01/01/Y 00:00:00');

        $dateDebut = DateTime::createFromFormat('j/m/Y H:i:s', $dateDeb);
        return $dateDebut;
    }

    /**
     * Retounr la date de fin d'année
     *
     * @return DateTime
     */
    public function FinDannee(): DateTime
    {
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $dateF = $date->format('31/12/Y 23:59:59');

        $dateFin = DateTime::createFromFormat('j/m/Y H:i:s', $dateF);
        return $dateFin;
    }

    /**
     * @return DateTime
     */
    public function debutMoisAvant():DateTime{
        $avant = date('01/m/Y 00:00:00', strtotime('-1 month'));

        $dateDebut = DateTime::createFromFormat('j/m/Y H:i:s', $avant);
        return $dateDebut;
    }
    public function finMoisAvant():DateTime{

        $avant = date('t/m/Y 23:59:59', strtotime('-1 month'));

        $dateDebut = DateTime::createFromFormat('j/m/Y H:i:s', $avant);
        return $dateDebut;
    }
}
