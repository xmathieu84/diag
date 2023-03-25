<?php

namespace App\Entity;

use App\Repository\SousDossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SousDossierRepository::class)
 */
class SousDossier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Dossier::class, inversedBy="sousDossiers")
     */
    private $dossier;

    /**
     * @ORM\OneToMany(targetEntity=DocSousDossier::class, mappedBy="sousDossier")
     */
    private $docSousDossiers;

    public function __construct()
    {


        $this->docSousDossiers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }





    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }


    /**
     * @return Collection|DocSousDossier[]
     */
    public function getDocSousDossiers(): Collection
    {
        return $this->docSousDossiers;
    }

    public function addDocSousDossier(DocSousDossier $docSousDossier): self
    {
        if (!$this->docSousDossiers->contains($docSousDossier)) {
            $this->docSousDossiers[] = $docSousDossier;
            $docSousDossier->setSousDossier($this);
        }

        return $this;
    }

    public function removeDocSousDossier(DocSousDossier $docSousDossier): self
    {
        if ($this->docSousDossiers->removeElement($docSousDossier)) {
            // set the owning side to null (unless already changed)
            if ($docSousDossier->getSousDossier() === $this) {
                $docSousDossier->setSousDossier(null);
            }
        }

        return $this;
    }
}
