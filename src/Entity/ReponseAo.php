<?php

namespace App\Entity;

use App\Repository\ReponseAoRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReponseAoRepository::class)
 */
class ReponseAo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $presentation;

    /**
     * @ORM\Column(type="text")
     */
    private $qualification;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contexte;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $precisionCom;

    /**
     * @ORM\OneToMany(targetEntity=DossierOtdAo::class, mappedBy="reponseAo")
     */
    private $dossierOtdAos;

    /**
     * @ORM\ManyToOne(targetEntity=AppelOffre::class, inversedBy="reponseAos")
     */
    private $appel;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="reponseAo")
     */
    private $contacts;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="reponseAos")
     */
    private $entreprise;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity=AppelOffre::class, mappedBy="reponseChoisie", cascade={"persist", "remove"})
     */
    private $appelOffre;



    public function __construct()
    {
        $this->dossierOtdAos = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->date = new DateTime('NOW', new DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getContexte(): ?string
    {
        return $this->contexte;
    }

    public function setContexte(?string $contexte): self
    {
        $this->contexte = $contexte;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getPrecisionCom(): ?string
    {
        return $this->precisionCom;
    }

    public function setPrecisionCom(?string $precisionCom): self
    {
        $this->precisionCom = $precisionCom;

        return $this;
    }

    /**
     * @return Collection|DossierOtdAo[]
     */
    public function getDossierOtdAos(): Collection
    {
        return $this->dossierOtdAos;
    }

    public function addDossierOtdAo(DossierOtdAo $dossierOtdAo): self
    {
        if (!$this->dossierOtdAos->contains($dossierOtdAo)) {
            $this->dossierOtdAos[] = $dossierOtdAo;
            $dossierOtdAo->setReponseAo($this);
        }

        return $this;
    }

    public function removeDossierOtdAo(DossierOtdAo $dossierOtdAo): self
    {
        if ($this->dossierOtdAos->removeElement($dossierOtdAo)) {
            // set the owning side to null (unless already changed)
            if ($dossierOtdAo->getReponseAo() === $this) {
                $dossierOtdAo->setReponseAo(null);
            }
        }

        return $this;
    }

    public function getAppel(): ?AppelOffre
    {
        return $this->appel;
    }

    public function setAppel(?AppelOffre $appel): self
    {
        $this->appel = $appel;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setReponseAo($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getReponseAo() === $this) {
                $contact->setReponseAo(null);
            }
        }

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAppelOffre(): ?AppelOffre
    {
        return $this->appelOffre;
    }

    public function setAppelOffre(?AppelOffre $appelOffre): self
    {
        // unset the owning side of the relation if necessary
        if ($appelOffre === null && $this->appelOffre !== null) {
            $this->appelOffre->setReponseChoisie(null);
        }

        // set the owning side of the relation if necessary
        if ($appelOffre !== null && $appelOffre->getReponseChoisie() !== $this) {
            $appelOffre->setReponseChoisie($this);
        }

        $this->appelOffre = $appelOffre;

        return $this;
    }


}
