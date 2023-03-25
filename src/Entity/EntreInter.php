<?php


namespace App\Entity;


class EntreInter
{
    private $entreprise;
    private $intervention;
    private $abonnement;
    private $totalInterDem;
    private $totalInterInsti;
    private $montantAbonnement;
    private $banque;

    /**
     * @return mixed
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * @param mixed $banque
     */
    public function setBanque($banque): void
    {
        $this->banque = $banque;
    }

    /**
     * @return mixed
     */
    public function getMontantAbonnement()
    {
        return $this->montantAbonnement;
    }

    /**
     * @param mixed $montantAbonnement
     */
    public function setMontantAbonnement($montantAbonnement): void
    {
        $this->montantAbonnement = $montantAbonnement;
    }

    /**
     * @return mixed
     */
    public function getTotalInterInsti()
    {
        return $this->totalInterInsti;
    }

    /**
     * @param mixed $totalInterInsti
     */
    public function setTotalInterInsti($totalInterInsti): void
    {
        $this->totalInterInsti = $totalInterInsti;
    }

    /**
     * @return mixed
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * @param Entreprise $entreprise
     */
    public function setEntreprise(Entreprise $entreprise): void
    {
        $this->entreprise = $entreprise;
    }

    /**
     * @return mixed
     */
    public function getIntervention():float
    {
        return $this->intervention;
    }

    /**
     * @param  $intervention
     */
    public function setIntervention( $intervention): void
    {
        $this->intervention = $intervention;
    }

    /**
     * @return mixed
     */
    public function getAbonnement()
    {
        return $this->abonnement;
    }

    /**
     * @param mixed $abonnement
     */
    public function setAbonnement($abonnement): void
    {
        $this->abonnement = $abonnement;
    }

    /**
     * @return mixed
     */
    public function getTotalInterDem()
    {
        return $this->totalInterDem;
    }

    /**
     * @param mixed $totalInterDem
     */
    public function setTotalInterDem($totalInterDem): void
    {
        $this->totalInterDem = $totalInterDem;
    }




}