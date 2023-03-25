<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailPrefectureRepository")
 */
class MailPrefecture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $numeroDepartement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomDepartement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailDepartement;

    /**
     * @ORM\OneToMany(targetEntity=Adresse::class, mappedBy="departement")
     */
    private $adresses;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
    }








    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDepartement(): ?int
    {
        return $this->numeroDepartement;
    }

    public function setNumeroDepartement(int $numeroDepartement): self
    {
        $this->numeroDepartement = $numeroDepartement;

        return $this;
    }

    public function getNomDepartement(): ?string
    {
        return $this->nomDepartement;
    }

    public function setNomDepartement(string $nomDepartement): self
    {
        $this->nomDepartement = $nomDepartement;

        return $this;
    }

    public function getMailDepartement(): ?string
    {
        return $this->mailDepartement;
    }

    public function setMailDepartement(string $mailDepartement): self
    {
        $this->mailDepartement = $mailDepartement;

        return $this;
    }

    /**
     * @return Collection|Adresse[]
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresse $adress): self
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses[] = $adress;
            $adress->setDepartement($this);
        }

        return $this;
    }

    public function removeAdress(Adresse $adress): self
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getDepartement() === $this) {
                $adress->setDepartement(null);
            }
        }

        return $this;
    }




}
