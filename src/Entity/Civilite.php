<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CiviliteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CiviliteRepository::class)
 *
 */
class Civilite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
         * @Groups ({"demandeur:read","demandeur:write"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Caractères non autorisés")
     * @Groups ({"demandeur:read","demandeur:write"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Caractères non autorisés")
     * @Groups ({"demandeur:read","demandeur:write"})
     */
    private $prenom;



    /**
     * @ORM\OneToOne(targetEntity=Salarie::class, mappedBy="civilite", cascade={"persist", "remove"})
     */
    private $salarie;


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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function fullName():?string{
        return $this->prenom.' '.$this->nom;
    }



    public function getSalarie(): ?Salarie
    {
        return $this->salarie;
    }

    public function setSalarie(?Salarie $salarie): self
    {
        $this->salarie = $salarie;

        // set (or unset) the owning side of the relation if necessary
        /*$newCivilite = null === $salarie ? null : $this;
        if ($salarie->getCivilite() !== $newCivilite) {
            $salarie->setCivilite($newCivilite);
        }*/

        return $this;
    }


}
