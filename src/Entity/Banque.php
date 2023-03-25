<?php

namespace App\Entity;

use App\Repository\BanqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BanqueRepository::class)
 */
class Banque
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="IBAN invalide")
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z]/",message="BIC invalide")
     */
    private $bic;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Nom de la banque invalide")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Adresse de votre banque invalide")
     */
    private $adresse;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="banques")
     */
    private $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity=Demandeur::class, inversedBy="banques")
     */
    private $institution;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sepaSigne;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sepa;







    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(string $bic): self
    {
        $this->bic = $bic;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getInstitution(): ?Demandeur
    {
        return $this->institution;
    }

    public function setInstitution(?Demandeur $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    public function getSepaSigne(): ?string
    {
        return $this->sepaSigne;
    }

    public function setSepaSigne(?string $sepaSigne): self
    {
        $this->sepaSigne = $sepaSigne;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getSepa(): ?string
    {
        return $this->sepa;
    }

    public function setSepa(?string $sepa): self
    {
        $this->sepa = $sepa;

        return $this;
    }








}
