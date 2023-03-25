<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\ListeInterTypeInterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ListeInterTypeInterRepository::class)
 * @ApiResource(itemOperations={"get"},collectionOperations={})
 */
class ListeInterTypeInter implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TypInter::class, inversedBy="listeInterTypeInters")
     * @Groups ({"listeInter:read","liti:read"})
     * @ApiSubresource()
     */
    private $typeInter;

    /**
     * @ORM\ManyToOne(targetEntity=ListeInter::class, inversedBy="listeInterTypeInters")
     */
    private $listeInter;

    /**
     * @ORM\OneToMany(targetEntity=TauxHoraire::class, mappedBy="inter")
     */
    private $tauxHoraires;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    public function __construct()
    {
        $this->tauxHoraires = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function jsonSerialize():array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeInter(): ?TypInter
    {
        return $this->typeInter;
    }

    public function setTypeInter(?TypInter $typeInter): self
    {
        $this->typeInter = $typeInter;

        return $this;
    }

    public function getListeInter(): ?listeInter
    {
        return $this->listeInter;
    }

    public function setListeInter(?listeInter $listeInter): self
    {
        $this->listeInter = $listeInter;

        return $this;
    }

    /**
     * @return Collection|TauxHoraire[]
     */
    public function getTauxHoraires(): Collection
    {
        return $this->tauxHoraires;
    }

    public function addTauxHoraire(TauxHoraire $tauxHoraire): self
    {
        if (!$this->tauxHoraires->contains($tauxHoraire)) {
            $this->tauxHoraires[] = $tauxHoraire;
            $tauxHoraire->setInter($this);
        }

        return $this;
    }

    public function removeTauxHoraire(TauxHoraire $tauxHoraire): self
    {
        if ($this->tauxHoraires->contains($tauxHoraire)) {
            $this->tauxHoraires->removeElement($tauxHoraire);
            // set the owning side to null (unless already changed)
            if ($tauxHoraire->getInter() === $this) {
                $tauxHoraire->setInter(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
