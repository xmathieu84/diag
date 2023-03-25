<?php

namespace App\Entity;

use App\Repository\DossierGeneralRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DossierGeneralRepository::class)
 */
class DossierGeneral
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\OneToMany(targetEntity=Piecesgenerale::class, mappedBy="dossierGeneral")
     */
    private $piecesgenerales;

    /**
     * @ORM\OneToMany(targetEntity=NoteGen::class, mappedBy="dossierGeneral")
     */
    private $noteGens;

    /**
     * @ORM\OneToOne(targetEntity=Dossier::class, mappedBy="dossierGeneral", cascade={"persist", "remove"})
     */
    private $dossier;

    /**
     * @ORM\OneToOne(targetEntity=DonneesGenerales::class, cascade={"persist", "remove"})
     */
    private $donneeGenerale;





    public function __construct()
    {
        $this->piecesgenerales = new ArrayCollection();
        $this->noteGens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    /**
     * @return Collection|Piecesgenerale[]
     */
    public function getPiecesgenerales(): Collection
    {
        return $this->piecesgenerales;
    }

    public function addPiecesgenerale(Piecesgenerale $piecesgenerale): self
    {
        if (!$this->piecesgenerales->contains($piecesgenerale)) {
            $this->piecesgenerales[] = $piecesgenerale;
            $piecesgenerale->setDossierGeneral($this);
        }

        return $this;
    }

    public function removePiecesgenerale(Piecesgenerale $piecesgenerale): self
    {
        if ($this->piecesgenerales->removeElement($piecesgenerale)) {
            // set the owning side to null (unless already changed)
            if ($piecesgenerale->getDossierGeneral() === $this) {
                $piecesgenerale->setDossierGeneral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NoteGen[]
     */
    public function getNoteGens(): Collection
    {
        return $this->noteGens;
    }

    public function addNoteGen(NoteGen $noteGen): self
    {
        if (!$this->noteGens->contains($noteGen)) {
            $this->noteGens[] = $noteGen;
            $noteGen->setDossierGeneral($this);
        }

        return $this;
    }

    public function removeNoteGen(NoteGen $noteGen): self
    {
        if ($this->noteGens->removeElement($noteGen)) {
            // set the owning side to null (unless already changed)
            if ($noteGen->getDossierGeneral() === $this) {
                $noteGen->setDossierGeneral(null);
            }
        }

        return $this;
    }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        // unset the owning side of the relation if necessary
        if ($dossier === null && $this->dossier !== null) {
            $this->dossier->setDossierGeneral(null);
        }

        // set the owning side of the relation if necessary
        if ($dossier !== null && $dossier->getDossierGeneral() !== $this) {
            $dossier->setDossierGeneral($this);
        }

        $this->dossier = $dossier;

        return $this;
    }

    public function getDonneeGenerale(): ?DonneesGenerales
    {
        return $this->donneeGenerale;
    }

    public function setDonneeGenerale(?DonneesGenerales $donneeGenerale): self
    {
        $this->donneeGenerale = $donneeGenerale;

        return $this;
    }


}
