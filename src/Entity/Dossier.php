<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DossierRepository::class)
 */
class Dossier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $createur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $proprietaire;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity=Adresse::class, cascade={"persist", "remove"})
     */
    private $adresse;



    /**
     * @ORM\ManyToOne(targetEntity=Demandeur::class, inversedBy="dossiers")
     */
    private $institution;





    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateModif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeModif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomModifiant;

    /**
     * @ORM\OneToMany(targetEntity=SousDossier::class, mappedBy="dossier")
     */
    private $sousDossiers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parcelle;

    /**
     * @ORM\OneToOne(targetEntity=DossierGeneral::class, inversedBy="dossier", cascade={"persist", "remove"})
     */
    private $dossierGeneral;

    public function __construct()
    {
        $this->piecesDossiers = new ArrayCollection();
        $this->date = New \DateTime('NOW',new \DateTimeZone('Europe/Paris'));
        $this->sousDossiers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getCreateur(): ?string
    {
        return $this->createur;
    }

    public function setCreateur(string $createur): self
    {
        $this->createur = $createur;

        return $this;
    }

    public function getProprietaire(): ?string
    {
        return $this->proprietaire;
    }

    public function setProprietaire(string $proprietaire): self
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): self
    {
        $this->adresse = $adresse;

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





    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(\DateTimeInterface $dateModif): self
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getTypeModif(): ?string
    {
        return $this->typeModif;
    }

    public function setTypeModif(string $typeModif): self
    {
        $this->typeModif = $typeModif;

        return $this;
    }

    public function getNomModifiant(): ?string
    {
        return $this->nomModifiant;
    }

    public function setNomModifiant(string $nomModifiant): self
    {
        $this->nomModifiant = $nomModifiant;

        return $this;
    }

    /**
     * @return Collection|SousDossier[]
     */
    public function getSousDossiers(): Collection
    {
        return $this->sousDossiers;
    }

    public function addSousDossier(SousDossier $sousDossier): self
    {
        if (!$this->sousDossiers->contains($sousDossier)) {
            $this->sousDossiers[] = $sousDossier;
            $sousDossier->setDossier($this);
        }

        return $this;
    }

    public function removeSousDossier(SousDossier $sousDossier): self
    {
        if ($this->sousDossiers->removeElement($sousDossier)) {
            // set the owning side to null (unless already changed)
            if ($sousDossier->getDossier() === $this) {
                $sousDossier->setDossier(null);
            }
        }

        return $this;
    }

    public function getParcelle(): ?string
    {
        return $this->parcelle;
    }

    public function setParcelle(?string $parcelle): self
    {
        $this->parcelle = $parcelle;

        return $this;
    }

    public function getDossierGeneral(): ?DossierGeneral
    {
        return $this->dossierGeneral;
    }

    public function setDossierGeneral(?DossierGeneral $dossierGeneral): self
    {
        $this->dossierGeneral = $dossierGeneral;

        return $this;
    }
}
