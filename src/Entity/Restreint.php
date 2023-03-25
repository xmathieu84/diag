<?php

namespace App\Entity;

use App\Repository\RestreintRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RestreintRepository::class)
 */
class Restreint
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private $delaiDepotCandidature;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private $delaiReponseCandidature;

    /**
     * @ORM\OneToOne(targetEntity=AppelOffre::class, inversedBy="restreint", cascade={"persist", "remove"})
     */
    private $appelOffre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDelaiDepotCandidature(): ?\DateInterval
    {
        return $this->delaiDepotCandidature;
    }

    public function setDelaiDepotCandidature(\DateInterval $delaiDepotCandidature): self
    {
        $this->delaiDepotCandidature = $delaiDepotCandidature;

        return $this;
    }

    public function getDelaiReponseCandidature(): ?\DateInterval
    {
        return $this->delaiReponseCandidature;
    }

    public function setDelaiReponseCandidature(\DateInterval $delaiReponseCandidature): self
    {
        $this->delaiReponseCandidature = $delaiReponseCandidature;

        return $this;
    }

    public function getAppelOffre(): ?AppelOffre
    {
        return $this->appelOffre;
    }

    public function setAppelOffre(?AppelOffre $appelOffre): self
    {
        $this->appelOffre = $appelOffre;

        return $this;
    }
}
