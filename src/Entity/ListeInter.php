<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ListeInterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ListeInterRepository::class)
 * @ApiResource (normalizationContext={"groups"={"etape1"}},
 *     itemOperations={"get"},collectionOperations={"get"})
 */
class ListeInter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups ({"listeInter:read","etape1","demandeur:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"listeInter:read","etape1","demandeur:read"})
     *
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=ListeInterTypeInter::class, mappedBy="listeInter")
     * @Groups ({"listeInter:read"})
     */
    private $listeInterTypeInters;

    /**
     * @ORM\OneToMany(targetEntity=Intervention::class, mappedBy="listeInter")
     */
    private $interventions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $raccourci;



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
            $listeInterTypeInter->setListeInter($this);
        }

        return $this;
    }

    public function removeListeInterTypeInter(ListeInterTypeInter $listeInterTypeInter): self
    {
        if ($this->listeInterTypeInters->contains($listeInterTypeInter)) {
            $this->listeInterTypeInters->removeElement($listeInterTypeInter);
            // set the owning side to null (unless already changed)
            if ($listeInterTypeInter->getListeInter() === $this) {
                $listeInterTypeInter->setListeInter(null);
            }
        }

        return $this;
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
            $intervention->setListeInter($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        if ($this->interventions->contains($intervention)) {
            $this->interventions->removeElement($intervention);
            // set the owning side to null (unless already changed)
            if ($intervention->getListeInter() === $this) {
                $intervention->setListeInter(null);
            }
        }

        return $this;
    }

    public function getRaccourci(): ?string
    {
        return $this->raccourci;
    }

    public function setRaccourci(string $raccourci): self
    {
        $this->raccourci = $raccourci;

        return $this;
    }
}
