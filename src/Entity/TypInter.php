<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TypInterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TypInterRepository::class)
 * @ApiResource(normalizationContext={"groups"={"listeInter:read","etape1"}},
 *     itemOperations={"get"},collectionOperations={})
 */
class TypInter implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups ({"listeInter:read","liti:read","etape1","demandeur:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"listeInter:read","liti:read","etape1","demandeur:read"})
     */
    private $nom;





    /**
     * @ORM\OneToMany(targetEntity=ListeInterTypeInter::class, mappedBy="typeInter")
     */
    private $listeInterTypeInters;

    

    /**
     * @ORM\OneToMany(targetEntity=Intervention::class, mappedBy="typeInter")
     */
    private $interventions;



    public function __construct()
    {
        $this->listeInterTypeInters = new ArrayCollection();
        $this->interventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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





    /**
     * @return Collection|ListeInterTypeInter[]
     */
    public function getListeInterTypeInters(): Collection
    {
        return $this->listeInterTypeInters;
    }

    public function addListeInterTypeInter(ListeInterTypeInter $listeInterTypeInter): self
    {
        if (!$this->listeInterTypeInters->contains($listeInterTypeInter)) {
            $this->listeInterTypeInters[] = $listeInterTypeInter;
            $listeInterTypeInter->setTypeInter($this);
        }

        return $this;
    }

    public function removeListeInterTypeInter(ListeInterTypeInter $listeInterTypeInter): self
    {
        if ($this->listeInterTypeInters->contains($listeInterTypeInter)) {
            $this->listeInterTypeInters->removeElement($listeInterTypeInter);
            // set the owning side to null (unless already changed)
            if ($listeInterTypeInter->getTypeInter() === $this) {
                $listeInterTypeInter->setTypeInter(null);
            }
        }

        return $this;
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

    

    /**
     * @return Collection|Intervention[]
     */
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): self
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions[] = $intervention;
            $intervention->setTypeInter($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        if ($this->interventions->contains($intervention)) {
            $this->interventions->removeElement($intervention);
            // set the owning side to null (unless already changed)
            if ($intervention->getTypeInter() === $this) {
                $intervention->setTypeInter(null);
            }
        }

        return $this;
    }
}
